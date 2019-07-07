<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class GoodsImg extends Model{
    public function addAll($goods_id){
        // 查找原来的图片信息
        // $old_info=$this->where('goods_id',$goods_id)->find();
        $list=[];
        // 获取File对象的数组
        $files=request()->file('imgs');
        // 循环上传文件并且生成缩略图
        foreach($files as $fileObj){
            $info=$fileObj->validate(['ext'=>'jpg,png'])->move('uploads');
            if(!$info){
                continue;
            }
            // 获取上传后的地址
            $goods_img=str_replace('\\','/',$info->getPathName());
            // 打开图片
            $img=\think\Image::open($goods_img);
            // 计算保存地址
            $goods_thumb='uploads/'.date('Ymd').'/thumb_'.$info->getFileName();
            $img->thumb(150,150)->save($goods_thumb);
            $list[]=[
                'goods_id'=>$goods_id,
                'goods_img'=>$goods_img,
                'goods_thumb'=>$goods_thumb
            ];
        }
        $this->saveALL($list);
    }
}