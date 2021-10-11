<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EmployeeModel;

class EmployeeController extends ResourceController
{
    //POST
    public function createEmployee()
    {
        $rules = [
            "name" => "required",
            "email" => "required|valid_email|is_unique[employees.email]",

        ];

        //validation
        if (!$this->validate($rules)) {
            //error

            $response = [
                'status' => 500,
                'message' => $this->validator->getErrors(),
                'error' => true,
                'data' => []
            ];
        } else {
            //no error
            $file = $this->request->getFile("profile_image");

            if (!empty($file)) {

                $imagen_name = $file->getName();
                $temp = explode(".", $imagen_name);
                $newImageName = round(microtime(true)) . '.' . end($temp);

                if ($file->move("images", $newImageName)) {
                    //imaage has benn uploaded
                    $emp_object = new EmployeeModel();
                    $data = [
                        "name" => $this->request->getVar('name'),
                        "email" => $this->request->getVar('email'),
                        "profile_image" => '/images/' . $newImageName
                    ];

                    if ($emp_object->insert($data)) {
                        //data has benn saved
                        $response = [
                            'status' => 200,
                            'message' => "Employee has been created",
                            'error' => false,
                            'data' => []
                        ];
                    } else {
                        $response = [
                            'status' => 500,
                            'message' => "Failed to create an employee",
                            'error' => false,
                            'data' => []
                        ];
                    }
                } else {
                    //failed to upload image
                    $response = [
                        'status' => 500,
                        'message' => "Failed to upload image",
                        'error' => false,
                        'data' => []
                    ];
                }
            } else {

                $emp_object = new EmployeeModel();
                $data = [
                    "name" => $this->request->getVar('name'),
                    "email" => $this->request->getVar('email')
                ];

                if ($emp_object->insert($data)) {
                    //data has benn saved
                    $response = [
                        'status' => 200,
                        'message' => "Employee has been created",
                        'error' => false,
                        'data' => []
                    ];
                } else {
                    $response = [
                        'status' => 500,
                        'message' => "Failed to create an employee",
                        'error' => false,
                        'data' => []
                    ];
                }
            }
        }

        return $this->respondCreated($response);
    }
    //GET
    public function listEmployees()
    {
        $emp_object = new EmployeeModel();
        $response = [
            'status' => 200,
            'message' => 'Employees list',
            'error' => false,
            'data' => $emp_object->findAll()
        ];
        return $this->respondCreated($response);
    }
    //GET
    public function singleEmployee($emp_id = 0)
    {
        $emp_object = new EmployeeModel();
        $emp_data = $emp_object->find($emp_id);

        if (is_numeric($emp_id) && $emp_id >= 0) {
            if (!empty($emp_data)) {
                //employee data
                $response = [
                    'status' => 200,
                    'message' => 'single Employee detail',
                    'error' => false,
                    'data' =>  $emp_data
                ];
            } else {
                //no employee found
                $response = [
                    'status' => 404,
                    'message' => 'No Employee found',
                    'error' => true,
                    'data' =>  []
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'Employee id must be numeric and no negative',
                'error' => true,
                'data' =>  []
            ];
        }


        return $this->respondCreated($response);
    }
    //POST -> PUT
    public function updateEmployee($emp_id)
    {
        //validation
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email'

        ];

        if (!$this->validate($rules)) {

            //validatino error
            $response = [
                'status' => 500,
                'message' => $this->validator->getErrors(),
                'error' => true,
                'data' =>  []
            ];
        } else {
            //we have no error
            $emp_object = new EmployeeModel();
            $emp_data = $emp_object->find($emp_id);

            if (!empty($emp_data)) {
                //check profile photoshop
                $file = $this->request->getFile('profile_image');

                if (!empty($file)) {

                    $image_name = $file->getName(); // test.png
                    $temp = explode('.', $image_name);
                    $new_image_name = round(microtime(true)) . '.' . end($temp); //unique name

                    if ($file->move('images', $new_image_name)) {

                        $update_data = [
                            'name' => $this->request->getVar('name'),
                            'email' => $this->request->getVar('email'),
                            'profile_image' => '/images/' . $new_image_name
                        ];
                        $emp_object->update($emp_id, $update_data);
                        $response = [
                            'status' => 200,
                            'message' => 'Employee data updated',
                            'error' => false,
                            'data' =>  []
                        ];
                    } else {
                        //failed to upload image
                        $response = [
                            'status' => 500,
                            'message' => 'Failed to upload image',
                            'error' => true,
                            'data' =>  []
                        ];
                    }
                } else {
                    //there is no fiel uploaded
                    $update_data = [
                        'name' => $this->request->getVar('name'),
                        'email' => $this->request->getVar('email')
                    ];
                    $emp_object->update($emp_id, $update_data);
                    $response = [
                        'status' => 200,
                        'message' => 'Employee data updated',
                        'error' => false,
                        'data' =>  []
                    ];
                }
            } else {
                //employee does not exist
                $response = [
                    'status' => 404,
                    'message' => 'No Employee found',
                    'error' => true,
                    'data' =>  []
                ];
            }
        }
        return $this->respondCreated($response);
    }
    //DELETE
    public function deleteEmployee($emp_id)
    {
        $emp_object = new EmployeeModel();
        $emp_data = $emp_object->find($emp_id);

        if (!empty($emp_data)) {
            //employe exits
            $emp_object->delete($emp_data);
            //employee does no exist
            $response = [
                'status' => 200,
                'message' => 'employee has been deleted',
                'error' => false,
                'data' =>  []
            ];
        } else {
            //employee does no exist
            $response = [
                'status' => 404,
                'message' => 'No Employee found',
                'error' => true,
                'data' =>  []
            ];
        }
        return $this->respondCreated($response);
    }
}
