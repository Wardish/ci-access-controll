<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access_controll {

    private $CI     = NULL;

    public function __construct($config = array())
    {
        $this->acl_file_path    = APPPATH . "/../acl.json";
        $this->acl              = json_decode(file_get_contents($this->acl_file_path), true);
        $this->CI               =& get_instance();
        $this->method           = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        $this->path             = "/" . $this->CI->uri->uri_string();
    }

    public function allowed(&$user_info)
    {
        if ( empty($this->acl) ) throw new Exception('ACL file is not found.');
        if ( empty($user_info['role']) ) throw new Exception('Role field is not found in $user_info.');

        $role = $user_info['role'];
        $req_path = $this->path;
        $req_method = $this->method;

        // リクエストされたパスはACLに存在する定義パスで始まっているか？
        // ex. strpos('/admin/login', '/admin') === 0　　定義あり。
        $path_matches = array_filter($this->acl['allow'],
                                     function($val, $path) use ($req_path) { return (strpos($req_path, $path) === 0); },
                                     ARRAY_FILTER_USE_BOTH);

        // ロングパスが実効
        $longest_path = "";
        foreach ($path_matches as $path => $value) {
            $longest_path = ( strlen($longest_path) < strlen($path) ) ? $path : $longest_path;
        }

        $longest_path_matches = array();
        $longest_path_matches[$longest_path] = $this->acl['allow'][$longest_path];

        // メソッドは許可に含まれているか？
        $method_matches = array_filter($longest_path_matches,
                                       function($var) use ($req_method) { return in_array($req_method, $var['method']);} );

        $result = array_filter($method_matches,
                               function($var) use ($role) {return in_array($role, $var['role']);});

        return (count($result) > 0);
    }

}
