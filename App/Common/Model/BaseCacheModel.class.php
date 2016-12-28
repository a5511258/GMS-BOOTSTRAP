<?php
namespace Common\Model;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseCacheEntity
 *
 * @author daniel
 */
abstract class BaseCacheModel
{
    private $cacheKey;
    private $cacheKeySuffix;
    private $remain;
    /**
     * php内存区，根据key->value进行存储.key=$cacheKey,value=实际值.
     * @var
     */
    static private $values;
    function __construct($cacheKeySuffix=null)
    {
        $this->cacheKeySuffix=$cacheKeySuffix;
        $this->cacheKey = $this->getCacheKey();
        $this->remain = $this->getCacheRemain();
    }


    /**
     * 取出缓存的值.
     * 1. 尝试从内存中取
     * 2. 尝试从Memcache中取
     * 3. 都没有则调用generateData进行生成.生成后调用$this->setValue.
     * @return mixed|string|void
     */
    public function getValue()
    {
        $value=self::$values[$this->cacheKey];
        if(isset($value))
        {
            return $value;
        }else{
            $value = S($this->getCacheKey());
            if (isset($value)&&$value) {
                self::$values[$this->cacheKey]=$value;
                DLog($this->getCacheKey() . " 缓存命中.");
            }else{
                DLog($this->getCacheKey() . " 缓存未命中.初始化ing");
                $value = $this->generateData();
                $this->setValue($value);
            }
            return $value;
        }
    }

    /**
     * 写入内存变量，并刷新缓存.
     * @param $value
     */
    public function setValue($value)
    {
        self::$values[$this->cacheKey]=$value;
        S($this->getCacheKey(), $value,$this->remain);
    }

    /**
     * 生成缓存数据
     * @return mixed
     */
    public abstract function generateData();

    /**
     * 返回缓存的key值
     * @return mixed
     */
    public function getCacheKey(){
        return $this->cacheKeySuffix? $this->getMainCacheKey().$this->cacheKeySuffix:$this->getMainCacheKey();
    }

    public abstract function getMainCacheKey();

    /**
     * 返回缓存时间
     * @return int
     */
    public function getCacheRemain()
    {
        return 15 * 60;
    }
}
