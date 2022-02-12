<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class User extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     * Return all data in here
     *
     */
    public function index()
    {
        $model = new UserModel();
        $data['username'] = $model->orderBy('id', 'ASC')->findAll();
        return $this->respond($data);
    }

    /**
     * Return the properties of a resource object
     * return just single data with rule or id in here
     *
     */
    public function show($id = null)
    {
        $model = new UserModel();
        $data = $model->where('id', $id)->first();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No user found');
        }
    }

    // /**
    //  * Return a new resource object, with default properties
    //  *
    //  * @return mixed
    //  */
    // public function new()
    // {
    //     //
    // }

    /**
     * Return a new resource object, with default properties
     *
     */
    public function debug()
    {
        $session = session();
        $session->set('abc', 'asdwdwdwdwd');
        if (isset($_SESSION['login'])){
            return 'yes';
        }
        else {
            return 'no';
        }
    }

    /**
     * Return a new resource object, with default properties
     *
     */
    public function login()
    {
        //mulai session
        $session = session();
        //ambil model user yang berisi konfigurasi tabel dan post input
        $model = new UserModel();

        //Ambil post data
        $post_data = [
            'username' => $this->request->getVar('username'),
            'password'  => $this->request->getVar('password'),
        ];

        //cek kalau username ada
        $db_data = $model->where('username', $post_data['username'])->first();
        //jika ada maka verifikasi passswordnya
        if ($db_data) {
            $user_db_id = $db_data['id'];
            $user_db_password = $db_data['password'];

            //kalau password salah kirim notifikasi
            if (password_verify($post_data['password'], $user_db_password) === false) {
                $response = [
                    'status'   => false,
                    'message' => 'User login failed, wrong password'
                ];

                return $this->respondCreated($response);
            }

            //jika password benar maka kirim data yang diperlukan ke front-end
            $response = [
                'data'    => ['userid' => $db_data['id'], 'username' => $db_data['username']],
                'status'    => 'true',
                'message'   => 'User login successful'
            ];

            //set session di back-end untuk keperluan lanjutan nantinya
            $session->set('login', true);
            $session->set('userid', $user_db_id);
            //kirim respon
            return $this->respondCreated($response);
        } 
        //jika username tidak ada maka kirim notifikasi
        else {
            $response = [
                'status'   => false,
                'message' => 'Unknown user'
            ];
            return $this->respondCreated($response);
        }
    }

    /**
     * Create a new resource object, from "posted" parameters
     * create new data in here
     */
    public function create()
    {
        //mulai session
        $session = session();
        //ambil model user yang berisi konfigurasi tabel dan post input
        $model = new UserModel();

        //Ambil post data
        $post_data = [
            'username' => $this->request->getVar('username'),
            'password'  => $this->request->getVar('password'),
        ];

        //hasing passwordnya
        $post_data['password'] = password_hash($post_data['password'], PASSWORD_BCRYPT);

        //masukkan semua data tadi ke database
        $model->insert($post_data);
        
        //kirim respon/notifikasi ke front-end
        $response = [
          'status'   => true,
          'error'    => null,
          'message' => 'User created successfully'
        ];
        return $this->respondCreated($response);
    }

    /**
     * Return the editable properties of a resource object
     * edit data in here
     *
     */
    // public function edit($id = null)
    // {
    //     return 'return edit()';
    // }

    /**
     * Add or update a model resource, from "posted" properties
     * update data in here
     *
     */
    //this should be create new password, edited password will be possible if use
    //encrypted password rather than hash password which is unsafe
    // public function update($id = null)
    // {
    //     $model = new UserModel();
    //     $id = $this->request->getVar('id');
    //     $data = [
    //         'name' => $this->request->getVar('name'),
    //         'password'  => $this->request->getVar('password'),
    //     ];

    //     $model->update($id, $data);
    //     $response = [
    //       'status'   => 200,
    //       'error'    => null,
    //       'message' => ['success' => 'User updated successfully']
    //     ];
    //     return $this->respond($response);
    // }

    /**
     * Delete the designated resource object from the model
     * delete data in here
     *
     */
    public function delete($id = null)
    {
        $model = new UserModel();
        $data = $model->where('id', $id)->delete($id);
        if($data){
            $model->delete($id);
            $response = [
                'status'   => true,
                'error'    => null,
                'message' => 'User successfully deleted'
            ];
            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound('No user found');
        }
    }
}
