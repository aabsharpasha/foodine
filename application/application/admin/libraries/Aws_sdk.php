<?php

// Require the Composer autoloader.
require 'aws-sdk/vendor/autoload.php';

use Aws\S3\S3Client;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Aws_sdk {

    public $s3Client, $ci,$bucket;

    public function __construct() {

        //$credentials = new Aws\Credentials\Credentials('AKIAIIA3PW6XV57EG2LQ', 'uuFkOHjk5aWuFK6WoncupeS4493ANP/dPSaWc5rX');
        $credentials = new Aws\Credentials\Credentials('AKIAJJEC4KHVFOFCJUTQ', '02JtfcyaA187ps5QiXAw+JOBTXXjabQzMTzddYRV');
        $this->s3Client = new Aws\S3\S3Client([
            'version' => 'latest',
            'region' => 'ap-southeast-1',
            'credentials' => $credentials
        ]);
	
	$this->bucket="hungermafiaprod";
	
    }

    public function __call($name, $arguments = null) {
        if (!property_exists($this, $name)) {
            return call_user_func_array(array($this->s3Client, $name), $arguments);
        }
    }

    /**
     * Wrapper of putObject with duplicate check.
     * If the file exists in bucket, it appends a unix timestamp to filename.
     * 
     * @param  array  $params same as putObject
     * @return result
     */
    public function saveObject($params = array()) {
	$result=array();
	$result['ObjectURL']="";
	try{
        $result = $this->putObject(array(
            'Bucket' => $this->bucket,
            'Key' => $params['Key'],
            'SourceFile' => $params['SourceFile'],
        ));
        chmod($params['SourceFile'],0777);
        }catch (Exception $e) {
            throw new Exception("Something went wrong in uploading a file.\n" . $e);
        }
        return $result['ObjectURL'];
    }

    /**
     * Wrapper for best practices in putting an object.
     * @param  array  $params: Bucket, Prefix, SourceFile, Key (filename)
     * @return string         URL of the uploaded object in s3
     */
    public function saveObjectInBucket($params = array()) {
        $error = null;
        // Create bucket
        try {
            $this->createBucket(array('Bucket' => $params['Bucket']));
        } catch (Exception $e) {
            throw new Exception("Something went wrong creating bucket for your file.\n" . $e);
        }
        // Poll the bucket until it is accessible
        try {
            $this->waitUntil('BucketExists', array('Bucket' => $params['Bucket']));
        } catch (Exception $e) {
            throw new Exception("Something went wrong waiting for the bucket for your file.\n" . $e);
        }
        // Upload an object
        $file_key = $params['Prefix'] . '/' . $params['Key'];
        $path = pathinfo($file_key);
        $extension = $path['extension'];
        $mimes = new Guzzle\Http\Mimetypes();
        $mimetype = $mimes->fromExtension($extension);
        try {
            $aws_object = $this->saveObject(array(
                        'Bucket' => $params['Bucket'],
                        'Key' => $file_key,
                        'ACL' => 'public-read',
                        'SourceFile' => $params['SourceFile'],
                        'ContentType' => $mimetype
                    ))->toArray();
        } catch (Exception $e) {
            throw new Exception("Something went wrong saving your file.\n" . $e);
        }
        // We can poll the object until it is accessible
        try {
            $this->waitUntil('ObjectExists', array(
                'Bucket' => $params['Bucket'],
                'Key' => $file_key
            ));
        } catch (Exception $e) {
            throw new Exception("Something went wrong polling your file.\n" . $e);
        }
        // Return result
        return $aws_object['ObjectURL'];
    }

    public function deleteImage($keyname="") {
	$result=array();
	try{
        $result = $this->deleteObject(array(
            'Bucket' => $this->bucket,
            'Key' => $keyname
        ));
	} catch (Exception $e) {
            throw new Exception("Something went wrong in deleting a file.\n" . $e);
        }
        
        return $result;
    }
    
    public function deleteDirectory($dir){
	$deleteDirectory=array();
	try{
        	$deleteDirectory=$this->deleteMatchingObjects($this->bucket, $dir);
	} catch (Exception $e) {
            throw new Exception("Something went wrong in deleting a folder.\n" . $e);
        }
	return $deleteDirectory;
    }

}
