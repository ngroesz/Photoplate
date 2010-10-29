<?php
class Albums extends Zend_Db_Table
{
    protected $_name = 'albums';
	 public function insert(array $data)
    {
        if (empty($data['created_at'])) {
            $data['created_at'] = date('Y-m-d h:i:s');
        }
		if (empty($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d h:i:s');
        }
		
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        if (empty($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d h:i:s');
        }
		
        return parent::update($data, $where);
    }
}
