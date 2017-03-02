<?php

/**
 * Copyright 2016 iPragmatech. All rights reserved.
 */
namespace Ipragmatech\Imagepath\Model;

use Ipragmatech\Imagepath\Api\ImagePathInterface;

/**
 * Class RegistryManagement
 * @package Ipragmatech\Imagepath\Model
 */
class Imagepath implements ImagePathInterface
{

    /**
     * get all image path for catalog items
     *
     * @api
     * @return array.
     */
    public function getImagPath(){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $storeId = $storeManager->getStore()->getId();


        /*$mediaPath = $objectManager
            ->get('Magento\Framework\App\Filesystem\DirectoryList')->getPath
            ('media');*/
        $mediaUrl = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);



        //generating directory//
        $diretoryArray = explode("/", $mediaUrl);
        array_pop($diretoryArray);
        $path = null;
        for ($i = 3; $i < sizeof($diretoryArray); $i++){
            $path = $path.$diretoryArray[$i].'/';
        }
        //end direcoty generation ////
        $dir = $path."catalog/product/cache/".$storeId."/";
        $array = array();
        /*try {*/
            foreach ($iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir,
                    \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $item) {
                // Note SELF_FIRST, so array keys are in place before values are pushed.

                $subPath = $iterator->getSubPathName();

                if ($item->isDir() && substr_count($subPath, '/') == 2) {
                    // Create a new array key of the current directory name.
                    if (strpos($subPath, "small_image") !== false) {

                        $array["small_image"][] = $this->getPathArray
                        ("small_image", $dir, $subPath);

                    } elseif (strpos($subPath, "thumbnail") !== false) {

                        $array["thumbnail"][] = $this->getPathArray("thumbnail",
                            $dir, $subPath);

                    } else {
                        $array["image"][] = $this->getPathArray("image", $dir,
                            $subPath);
                    }
                }

            }
            //removing duplicate element fro array
            if(array_key_exists("image", $array)) {
                $array["image"] = array_values(array_unique($array["image"],
                    SORT_REGULAR));
            }
            if(array_key_exists("thumbnail", $array)) {
                $array["thumbnail"] = array_values(array_unique($array["thumbnail"],
                    SORT_REGULAR));
            }
            if(array_key_exists("small_image", $array)) {
                $array["small_image"] = array_values(array_unique($array["small_image"],
                    SORT_REGULAR));
            }

            $data[] = $array;
            return $data;

        /*}catch (\Throwable $e){
            $msg [] = [
                "status" => false,
                "msg" => "No cache generated yet, ".$e->getMessage()
            ];
            return $msg;
        }*/
    }

    /**
     * function to get array of image sizes
     * @param $type
     * @param $path
     * @return array
     */
    private  function getPathArray($type, $basePath, $subPath){
        $sizes = explode("/", $subPath);
        $array = [];
        if(count($sizes) > 2 &&  substr_count($sizes[1], 'x') == 1){
            $dimension = explode("x",$sizes[1]);
            if(count($dimension)== 2){
                $array = array
                (
                    "h"=>strlen($dimension[1]) == 0?$dimension[0]:$dimension[1],
                    "w"=>$dimension[0],
                    "path"=>$basePath.$subPath
                );
            }
        }else{
            array_pop($sizes);
            $subPath = implode("/", $sizes);
            $array = array(
                "h"=>"original",
                "w"=>"original",
                "path"=>$basePath.$subPath
            );
        }
        return $array;
    }
}
