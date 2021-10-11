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
    }
    //GET
    public function singleEmployee($emp_id)
    {
    }
    //POST -> PUT
    public function updateEmployee($emp_id)
    {
    }
    //DELETE
    public function deleteEmployee($emp_id)
    {
    }
}
