<?php

class Group extends Model
{
    public function createGroup($openId,$groupName,$intro){
        //findAndModify查找并更新函数，具有原子性
        $locations=$this->collection->findAndModify(array('_id'=>'groupId'),array('$inc'=>array('id'=>1)),array(),array('new'=>true));
        //格式化groupId
        $groupId = sprintf("%08d", $locations['id']);
        $groupInfo = array(
            "_id" => $groupId,
            "createUser" => $openId,
            "groupName" => $groupName,
            "createTime" =>  date('Y-m-d H:i:s',time()),
            "groupNum" => 1,
            "groupIntro" => $intro
        );
        $options = array('w'=>true);
        try {
            $this->collection->insert($groupInfo,$options);
            return $groupId;
        }
        catch (MongoCursorException $e) {
            return false;
        }
    }

    public function getGroupNameByIdList($groupIdList){
        $res = $this->collection->find(array(
            "_id" => array('$in' => $groupIdList)
        ));
        $list = array();
        foreach ($res as $groupInfo) {
            $arr = array(
                'groupId' => $groupInfo['_id'],
                'groupName' => $groupInfo['groupName']
            );
            $list = array_merge_recursive($list,array($arr));
        }
        return $list;
    }

    public function getMember($groupId){
        $memberList = $this->collection->findOne(array("_id" => $groupId),array("member"));
        $list = array();
        foreach ($memberList['member'] as $name) {
            $arr = array(
                'name' => $name
            );
            $list = array_merge_recursive($list,array($arr));
        }
        return $list;
    }

    public function getGroupNameById($groupId){
        return $this->collection->findOne(array("_id" => $groupId),array('groupName'));
    }

    public function getMemberListWithGroupName($groupId){
        return $this->collection->findOne(array("_id" => $groupId),array("member","groupName"));
    }

    public function findGroupInfo($groupId){
        return $this->collection->findOne(array("_id" => $groupId));
    }

    public function joinGroup($groupId,$openId,$name){
        try {
            $this->collection->update(
                array('_id'=>$groupId),
                array('$set'=>array("member.$openId" =>$name),'$inc'=>array('groupNum'=>1)),
                array('w'=>true)
            );
            return true;
        }
        catch (MongoCursorException $e) {
            return false;
        }
    }

    public function checkIsCreate($groupId,$openId){
        try {
            $res = $this->collection->findOne(array('createUser' => $openId,'_id' => $groupId));
            if($res){
                return true;
            }else{
                return false;
            }
        }
        catch (MongoCursorException $e) {
            return false;
        }
    }

    public function checkInGroup($groupId,$openId){
        try {
            $res = $this->collection->findOne(array('_id' => $groupId),array("member.$openId"));
            if(!empty($res["member"])){
                return true;
            }else{
                return false;
            }
        }
        catch (MongoCursorException $e) {

        }
    }

    public function updateGroup($groupId,$groupName,$intro){
        try {
            $this->collection->update(
                array('_id'=>$groupId),
                array('$set'=>array('groupName' => $groupName,'groupIntro' =>$intro))
            );
            return true;
        }
        catch (MongoCursorException $e) {
            return false;
        }
    }

    public function dropGroup($groupId){
        try {
            $this->collection->remove(array("_id" => $groupId),array("justOne" => true));
            return true;
        }
        catch (MongoCursorException $e) {
            return false;
        }
    }

    public function quitGroup($groupId,$openId){
        try {
            $this->collection->update(
                array('_id'=>$groupId),
                array('$unset'=>array("member.$openId"=>-1),'$inc'=>array('groupNum'=>-1)),
                array('w'=>true)
            );
            return true;
        }
        catch (MongoCursorException $e) {
            return false;
        }
    }

    public function updateName($groupId,$openId,$newName){
        try {
            $this->collection->update(
                array('_id'=>$groupId),
                array('$set'=>array("member.$openId" =>$newName)),
                array('w'=>true)
            );
            return true;
        }
        catch (MongoCursorException $e) {
            return false;
        }
    }
}