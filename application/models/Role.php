<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class Role extends CI_Model
{
    protected $table = 'roles';
    protected $key = 'role_id';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * Insert new record
     *
     * @param array $data
     * @return boolean whether or not the insert was done
     */
    public function insert($data){
        $this->db->trans_begin();
        $this->db->insert($this->table, ['role_name' => $data['role_name']]); //insert role
        $insert_id = $this->db->insert_id();
        foreach($data['permissions'] as $permission){ //insert role-permission 
            $this->db->insert('role_has_permissions', ['roles_role_id' => $insert_id, 'permissions_permission_id' => $permission]);
        }

        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
    
    /**
     * Get single record
     *
     * @param int record id
     * @return array record
     */
    public function get($id){
        return $this->db
            ->select('role_id, role_name, permission_name, roles.create_time, roles.update_time')
            ->join('role_has_permissions', 'role_has_permissions.roles_role_id = roles.role_id')
            ->join('permissions', 'permissions.permission_id=role_has_permissions.permissions_permission_id')
            ->where($this->key, $id)
            ->where('roles.delete_time', NULL)
            ->get($this->table);
    }
    
    /**
     * Get all records.
     *
     * @param string role
     * @return array record(s)
     */
    public function getAll(){
        return $this->db
            ->select('role_id, role_name, permission_name, roles.create_time, roles.update_time')
            ->join('role_has_permissions', 'role_has_permissions.roles_role_id = roles.role_id')
            ->join('permissions', 'permissions.permission_id=role_has_permissions.permissions_permission_id')
            ->where('roles.delete_time', NULL)
            //->group_by([$this->key, 'role_name'])
            ->get($this->table);
    }

    /**
     * Update a given role record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data){ //@todo, implement update on permissions as well
        $data['update_time'] = date('y-m-d H:i:s');
        $this->db->trans_begin();
        //remove all permissions from this role
        $this->db->where('role_has_permissions.roles_role_id', $id)->delete('role_has_permissions');
        //add the new permissions
        foreach($data['permissions'] as $permission){
            $this->db->insert('role_has_permissions', ['roles_role_id' => $id, 'permissions_permission_id'=> $permission]);
        }
        //update role name
        $this->db
                ->set(['role_name' => $data['role_name']])
                ->where($this->key, $id)
                ->where('delete_time', NULL)
                ->update($this->table);

        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @return boolean deleted or not
     */
    public function delete($id){
        //find users using this role, if there are any users, cancel delete operation
        $users = $this->db
            ->where('roles_role_id', $id)
            ->where('delete_time', NULL)
            ->get('users')
            ->num_rows();
        if($users == 0)
            return $this->db
                    ->set(['delete_time' => date('Y-m-d H:i:s')])
                    ->where('role_id', $id)
                    ->update($this->table);
        else 
            return false;
    }

}