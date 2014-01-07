<?php

/**
 * Safe Image Upload: Image redrawing and uploading class
 *
 * @author  Kim Dehmlow <kim@bettercollective.com>
 * @since   2012-11-14
 * @version 2013-09-11
 *
 */
class SafeImageUploader {
    private $_intMaxFilesize = 4194304; // bytes (4 MB)
    private $_intImageQuality = 75;
    private $_blnsaveAsPNG = false;
    private $_blnAcceptBase64 = false;
    private $_blnForceResize = false;
    private $_arrDimensions = array('width' => 0, 'height' => 0);
    private $_strUploadPath;
    private $_strTempPath;
    private $_arrApprovedTypes = array(
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/pjpeg' => 'jpg'
    );

    /**
     * Construct the SafeImageUploader class and set defaults
     *
     * @param string $strPath A complete path to an existing destination folder on the server
     * @param array $arrTypes (optional) An array containg the accepted image mime-types as key and corresponding file extension as value
     * @param int $intFilesize (optional) The maximum allowed filesize in bytes.
     */
    function __construct($strPath, $arrTypes = array(), $intFilesize = 0) {
        $this->_strUploadPath = $strPath;
        $this->_strTempPath = $strPath . 'temp/';

        if(is_array($arrTypes) && count($arrTypes) > 0) {
            $this->_arrApprovedTypes = $arrTypes;
        }

        if(is_numeric($intFilesize)) {
            $intFilesize = intval($intFilesize);
            if($intFilesize > 0) {
                $this->_intMaxFilesize = $intFilesize;
            }
        }
    }

    /**
     * SafeImageUploader's main function which combines all other functions into one quick call
     *
     * @return array $arrData Structure: (string)'filename', (int)'status', (bool)'success' and (string)'error'
     */
    public function safeSave() {
        $arrData = array(
            'filename' => '',
            'status' => 0,
            'success' => false,
            'error' => ''
        );

        $arrImageData = $this->checkImageData();
        if(isset($arrImageData['error'])) {
            $arrData['status'] = 6;
            $arrData['error'] = $arrImageData['error'];
            return $arrData;
        } else {
            $objImage = $arrImageData['data'];
            $strType = $arrImageData['type'];
            $strExt = $arrImageData['ext'];
        }

        if($this->checkImageSize($arrImageData)) {
            $strName = $this->saveTemporaryImage($objImage, $strType, $strExt);
            if($strName) {
                $objNewImage = $this->redrawImage($strName, $strExt);
                if($objNewImage) {
                    $strFilename = $this->saveNewImage($objNewImage, $strName);
                    if($strFilename) {
                        if($this->deleteTemporaryImage($strName, $strExt)) {
                            $arrData['filename'] = $strFilename;
                            $arrData['success'] = true;
                        } else {
                            $arrData['status'] = 1;
                            $arrData['error'] = 'The temporary image could not be removed';
                        }
                    } else {
                        $arrData['status'] = 2;
                        $arrData['error'] = 'The new image could not be saved';
                    }
                } else {
                    $arrData['status'] = 3;
                    $arrData['error'] = 'Image processing failed';
                }
            } else {
                $arrData['status'] = 4;
                $arrData['error'] = 'The temporary image could not be saved';
            }
        } else {
            $arrData['status'] = 5;
            $arrData['error'] = 'Image is too large';
        }
        return $arrData;
    }

    /*
        Checks if the recieved image is a valid filetype
        @return arrReturn Success: Array[(object)'data', (string)'type', (string)'ext'] - Error: Array[(string)'error']
    */
    public function checkImageData() {
        if(isset($_REQUEST['base64']) && $this->_blnAcceptBase64) {
            $strBase64 = $_REQUEST['base64'];
            $strBase64 = explode(',', $strBase64, 2);
            $strBase64 = $strBase64[1];
            $objImage = base64_decode($strBase64);
            if($objImage) {
                $strType = 'base64';
                $strTempExt = 'png';
            } else {
                $strError = 'Error: Not valid base64 image';
            }
        } else if(isset($_FILES['image'])) {
            if(isset($this->_arrApprovedTypes[$_FILES['image']['type']])) {
                $strType = 'file';
                $objImage = $_FILES['image'];
                $strTempExt = $this->_arrApprovedTypes[$objImage['type']];
            } else {
                $strError = 'Error: Filetype not allowed';
            }
        } else {
            $strError = 'Error: Recieved data not recognised';
        }
        if(isset($strError)) {
            $arrReturn = array(
                'error' => $strError
            );
        } else {
            $arrReturn = array(
                'data' => $objImage,
                'type' => $strType,
                'ext' => $strTempExt
            );
        }
        return $arrReturn;
    }

    /*
        Checks if the recieved image is too large
        @param arrData (array) Data on the image to check ('data', 'type')
        @return blnSizeAccept Success: (bool)true - Error: (bool)false
    */
    public function checkImageSize($arrData) {
        if($arrData['type'] == 'base64') {
            $intSize = strlen($arrData['data']);
        } else if($arrData['type'] == 'file') {
            $intSize = $arrData['data']['size'];
        } else {
            return false;
        }
        $blnSizeAccept = ($intSize < $this->_intMaxFilesize ? true : false);
        return $blnSizeAccept;
    }

    /*
        Checks if the recieved image is too large
        @param objImage (object) Image that was recieved
        @param strType (string) The type of the recieved image
        @param strTempExt (string) The file extension of the recieved image
        @return strName Success: (string) Clean filename without extension - Error: (bool) false
    */
    public function saveTemporaryImage($objImage, $strType, $strTempExt) {
        $intTimestamp = time();
        $intRandom = rand(5, 15);
        $strName = $strType . '-' . $intTimestamp . '-' . $intRandom;
        $strTempFile = $this->formatTempPath($strName, $strTempExt);
        if($strType == 'base64') {
            file_put_contents($strTempFile, $objImage);
        } else {
            move_uploaded_file($objImage['tmp_name'], $strTempFile);
        }
        if(file_exists($strTempFile)) {
            return $strName;
        } else {
            return false;
        }
    }

    /*
        Processes the temporary image: Redraw every pixel and resize
        @param strName (string) Clean filename that was saved in the temporary folder
        @param strTempExt (string) The file extension of the image
        @return objFinalImage Success: (object) A gd imageobject - Error: (bool) false
    */
    public function redrawImage($strName, $strTempExt) {
        $strTempFile = $this->formatTempPath($strName, $strTempExt);
        list($intTempWidth, $intTempHeight) = getimagesize($strTempFile);
        if($strTempExt == 'png') {
            $objImage = imagecreatefrompng($strTempFile);
        } else if($strTempExt == 'jpg') {
            $objImage = imagecreatefromjpeg($strTempFile);
        } else if($strTempExt == 'gif') {
            $objImage = imagecreatefromgif($strTempFile);
        } else if($strTempExt == 'gd') {
            $objImage = imagecreatefromgd($strTempFile);
        } else {
            return false;
        }

        $objNewImage = imagecreatetruecolor($intTempWidth+10, $intTempHeight+10);
        imagealphablending($objNewImage, false);
        imagesavealpha($objNewImage, true);
        for($x = 0; $x < $intTempWidth; $x += 1) {
            for($y = 0; $y < $intTempHeight; $y += 1) {
                $intColorIndex = imagecolorat($objImage, $x, $y);
                $objTempColor = imagecreatetruecolor(1, 1);
                $arrColors = imagecolorsforindex($objImage, $intColorIndex);
                $intColor = imagecolorallocatealpha($objTempColor, $arrColors['red'], $arrColors['green'], $arrColors['blue'], $arrColors['alpha']);
                imagesetpixel($objNewImage, $x+10, $y+10, $intColor);
            }
        }

        $arrNewSize = $this->calculateSize($intTempWidth, $intTempHeight);
        $objFinalImage = imagecreatetruecolor($arrNewSize['width'], $arrNewSize['height']);
        imagecopyresampled($objFinalImage, $objNewImage, 0, 0, 10, 10, $arrNewSize['width'], $arrNewSize['height'], $intTempWidth, $intTempHeight);

        return $objFinalImage;
    }

    /*
        Saves the processed image to the upload path
        @param objNewImage (object) A gd imageobject
        @param strName (string) Clean filename (no extension)
        @return strFilename Success: (string) Filename with extension - Error: (bool) false
    */
    public function saveNewImage($objNewImage, $strName) {
        if($this->_blnsaveAsPNG === true) {
            $strExt = 'png';
            $strFilePath = $this->formatUploadPath($strName, $strExt);
            imagepng($objNewImage, $strFilePath, $this->_intImageQuality);
        } else {
            $strExt = 'jpg';
            $strFilePath = $this->formatUploadPath($strName, $strExt);
            imagejpeg($objNewImage, $strFilePath, $this->_intImageQuality);
        }
        if(file_exists($strFilePath)) {
            $strFilename = $strName .'.'. $strExt;
            return $strFilename;
        } else {
            return false;
        }
    }

    /*
        Sets the maximum dimensions of the image
        @param arrSize (array) Containing 'width' and 'height' as key
        @return Success: (bool) true - Error: (bool) false
    */
    public function setMaxDimensions($arrSize) {
        if(isset($arrSize['width']) && isset($arrSize['height'])) {
            $this->_arrDimensions = array(
                'width' => (int)$arrSize['width'],
                'height' => (int)$arrSize['height']
            );
            return true;
        } else {
            return false;
        }
    }

    /*
        Calculates the width and height to fit the maximum dimensions if they are set
        @param intTempWidth (int) Width of the temporary image
        @param intTempHeight (int) Height of the temporary image
        @return arrReturn Array[(int)'width', (int)'height']
    */
    private function calculateSize($intTempWidth, $intTempHeight) {
        $intConfigWidth = $this->_arrDimensions['width'];
        $intConfigHeight = $this->_arrDimensions['height'];
        $intRatio = $intTempWidth / $intTempHeight;

        if($intConfigWidth > 0 || $intConfigHeight > 0) {
            if($this->_blnForceResize) {
                if($intConfigWidth > 0 && $intConfigHeight === 0) {
                    $arrReturn['width'] = $intConfigWidth;
                    $arrReturn['height'] = round($intConfigWidth / $intRatio);
                } else if($intConfigWidth === 0 && $intConfigHeight > 0) {
                    $arrReturn['width'] = round($intConfigHeight * $intRatio);
                    $arrReturn['height'] = $intConfigHeight;
                } else if($intConfigWidth > 0 && $intConfigHeight > 0) {
                    $arrReturn['width'] = $intConfigWidth;
                    $arrReturn['height'] = $intConfigHeight;
                } else {
                    return false;
                }
                return $arrReturn;
            } else if($intConfigWidth > 0 && $intTempWidth > $intConfigWidth) {
                $arrReturn['width'] = $intConfigWidth;
                $arrReturn['height'] = round($intConfigWidth / $intRatio);
            } else if($intConfigHeight > 0 && $intTempHeight > $intConfigHeight) {
                $arrReturn['width'] = round($intConfigHeight * $intRatio);
                $arrReturn['height'] = $intConfigHeight;
            } else {
                $arrReturn['width'] = $intTempWidth;
                $arrReturn['height'] = $intTempHeight;
            }
        } else {
            $arrReturn['width'] = $intTempWidth;
            $arrReturn['height'] = $intTempHeight;
        }
        return $arrReturn;
    }

    /*
        Deletes the temporary image file
        @param strName (string) Clean filename that was saved in the temporary folder
        @param strExt (string) The file extension of the image
        @return Success: (bool) true - Error: (bool) false
    */
    private function deleteTemporaryImage($strName, $strExt) {
        $strFilepath = $this->formatTempPath($strName, $strExt);
        unlink($strFilepath);
        if(!file_exists($strFilepath)) {
            return true;
        } else {
            return false;
        }
    }

    /*
        Get the complete filepath for the temporary image
        @param strName (string) Clean filename (no extension)
        @param strExt (string) The file extension of the image
        @return strFilepath (string)Path to the image on the server
    */
    public function formatTempPath($strName, $strExt) {
        $strFilepath = $this->_strTempPath . $strName . '.' . $strExt;
        return $strFilepath;
    }

    /*
        Get the complete filepath for the final image
        @param strName (string) Clean filename (no extension)
        @param strExt (string) The file extension of the image
        @return strFilepath (string)Path to the image on the server
    */
    public function formatUploadPath($strName, $strExt) {
        $strFilepath = $this->_strUploadPath . $strName . '.' . $strExt;
        return $strFilepath;
    }

    /*
        Change the temporary folder
        @param strPath (string) Complete path to an existing folder
        @return false
    */
    public function setTemporaryPath($strPath) {
        $this->_strTempPath = $strPath;
        return false;
    }

    /*
        Set the final image format to PNG
        @param intQuality Optional: (int) Compression valid values: 0 - 9
        @return false
    */
    public function saveAsPNG($intQuality = 7) {
        $this->_blnsaveAsPNG = true;
        $this->_intImageQuality = $intQuality;
        return false;
    }

    /*
        Set the final image format to JPG
        @param intQuality Optional: (int) Compression valid values: 0 - 100
        @return false
    */
    public function saveAsJPG($intQuality = 75) {
        $this->_blnsaveAsPNG = false;
        $this->_intImageQuality = $intQuality;
        return false;
    }

    /*
        Set image processing to resie every image to the maximum dimensions (will ignore ratio if both width and height are set!)
        @param blnForce (bool)
        @return false
    */
    public function setForceResize($blnForce) {
        $this->_blnForceResize = $blnForce;
        return false;
    }

    /*
        Set to allow/disallow base64 upload
        @param blnAccept (bool)
        @return false
    */
    public function setBase64($blnAccept) {
        $this->_blnAcceptBase64 = $blnAccept;
        return false;
    }
}