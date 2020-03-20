<?php

use Illuminate\Support\Facades\Config;

$api_version = Config::get('openstyle.shopify.api_version');

return array(

    /*
    |--------------------------------------------------------------------------
    | Operations
    |--------------------------------------------------------------------------
    |
    | This array of operations is translated into methods that complete these
    | requests based on their configuration.
    |
    */

    "operations" => array(

        /**
         *    getUsers() method
         *
         *    reference: http://docs.shopify.com/api/user
         */
        "getUsers" => array(
            "httpMethod" => "GET",
            "uri" => "/admin/api/{$api_version}/users.json",
            "summary" => "Get a list of all users.",
            "responseModel" => "defaultJsonResponse",
        ),
        
        
        /**
         *    getUser() method
         *
         *    reference: http://docs.shopify.com/api/user
         */
        "getUser" => array(
            "httpMethod" => "GET",
            "uri" => "/admin/api/{$api_version}/users/{id}.json",
            "summary" => "Get a user.",
            "responseModel" => "defaultJsonResponse",
            "parameters" => array(
                "id" => array(
                    "type" => "number",
                    "location" => "uri",
                    "description" => "The ID of the user.",
                    "required" => true
                )
            )
        )
        
    ),

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | This array of models is specifications to returning the response
    | from the operation methods.
    |
    */

    "models" => array(

    ),
);