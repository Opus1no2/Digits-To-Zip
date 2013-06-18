<?php
/**
 *
 * Conveniance Class for creating an array
 * of Canadian zip codes from a series of digits.
 *
 * @author Travis Tillotson | tillotson.travis@gmail.com
 */
class DigitsToZip
{
    /**
     * @var int $_data | Zip code in digits
     */
    protected $_data;
    
    const VALID_LENGTH = 6;
    
    /**
     *
     * Mapping for the 'F' in the forward sorting order
     *
     * @param array $f
     */
    protected static $f = array(
        'x' => 9, 
        'y' => 9, 
        'v' => 8, 
        't' => 8, 
        's' => 7, 
        'r' => 7, 
        'p' => 7, 
        'j' => 5, 
        'g' => 4, 
        'a' => 2, 
        'e' => 3, 
        'c' => 2, 
        'b' => 2, 
        'h' => 4, 
        'k' => 5, 
        'm' => 6, 
        'l' => 5, 
        'n' => 6
    );
    
    /**
     * The chars 'd', 'f', 'i', 'o', 'q', 'u'
     * are not allowed as the 3rd or 5th letter
     *
     * @param array $map
     */
    protected static $map = array(
        2 => array('a', 'b', 'c'),
        3 => array('e'),
        4 => array('g', 'h'),
        5 => array('j', 'k', 'l'),
        6 => array('m', 'n'),
        7 => array('p', 'r', 's'),
        8 => array('t', 'v'),
        9 => array('w', 'x', 'y', 'z')
    );
    
    /**
     * Valid numbers
     *
     * @param array $s
     */
    protected static $s = array(
        0,1,2,3,4,5,6,7,8,9
    );
    
    /**
     *
     * Get zip at instantiation
     *
     * @param string $data
     * 
     * @return void
     */
    public function __construct($data)
    {
        $this->_data = (string)$data;
    }
    
    /**
     *
     * Wrapper for checking the proper length
     *
     * @return void
     *
     * @throws RunTimeException
     */
    public function checkLength()
    {
        if (!strlen($this->_data) == self::VALID_LENGTH) {
            throw new RunTimeException('Invalid legth');
        }
    }
    
    /**
     *
     * Validate the first alpha of the zip code
     *
     * @return string
     *
     * @throws RunTimeException
     */
    public function validateF()
    {
        $possible_F = array();

        if (in_array($this->_data[0],  self::$f)) {
            foreach (self::$f as $k => $v) {
                if ($this->_data[0] == $v) {
                    $possible_F[] = $k;
                }
            }
        } else {
            throw new RunTimeException('Invalid F for forward sortation area');
        }
        return $possible_F;
    }
    
    /**
     *
     * Make sure our number is 0…9
     *
     * @param string $num
     *
     * @return string
     *
     * @throws RunTimeException
     */
    public function validateNumber($num)
    {
        if (in_array($num, self::$s)) {
            return $num;
        }
        throw new RunTimeException('Invalid Number');
    }
    
    /**
     *
     * Map a digit to a phone keypad number
     *
     * @param int $digit
     *
     * @return array
     *
     * @throws RunTimeException
     */
    public function map($digit)
    {
        if (array_key_exists($digit, self::$map)) {
            return self::$map[$digit];
        }
        throw new RunTimeException('Invalid Number');
    }
    
    /**
     *
     * Retreive the first two chars of the postal code
     *
     * @return array
     */
    protected function _getFS()
    {
        $f = $this->validateF();
        $s = $this->validateNumber($this->_data[1]);
        
        $possible_fs = array();
        
        foreach ($f as $v) {
            $possible_fs[] = array($v, $s);
        }
        return $possible_fs;
    }
    
    /**
     *
     * Retrieve the first three chars of the postal code
     *
     * @param array $possible_fs | first two chars
     *
     * @return array
     */
    private function _getFSA($possible_fs)
    {
        $a = $this->map($this->_data[2]);
        
        $possible_fsa = array();
        
        foreach ($a as $area) {
            foreach ($possible_fs as $fs) {
                $fs[] = $area;
                $possible_fsa[] = $fs;
                unset($fs);
            }
        }
        return $possible_fsa;
    }
    
    /**
     *
     * Retrieve the first four chars of the postal code
     *
     * @param array $possible_fsa | first three chars
     *
     * @return array
     */
    private function _getFSAL($possible_fsa)
    {
        $l = $this->validateNumber($this->_data[3]);
        
        $possible_fsal = array();
        
        foreach ($possible_fsa as $fsa) {
             $fsa[] = $l;
             $possible_fsal[] = $fsa;
             unset($fsa);
        }
        return $possible_fsal;
    }
    
    /**
     *
     * Retrieve the first five chars of the postal code
     *
     * @param array $possible_fsal | first four chars
     *
     * @return array 
     */
    private function _getFSALD($possible_fsal)
    {
        $d = $this->map($this->_data[4]);
        
        $possible_fsald = array();
        
        foreach ($d as $ldu) {
            foreach ($possible_fsal as $fsal) {
                $fsal[] = $ldu;
                $possible_fsald[] = $fsal;
                unset($fsal);
            }
        }
        return $possible_fsald;
    }
    
    /**
     *
     * Retreieve the entire postal code
     *
     * @param array $possible_fsald | first five chars
     *
     * @return array
     */
    private function _getFSALDU($possible_fsald)
    {
        $u = $this->validateNumber($this->_data[5]);
        
        $possible_fsaldu = array();
        
        foreach ($possible_fsald as $fsald) {
            $fsald[] = $u;
            $possible_fsaldu[] = $fsald;
        }
        return $possible_fsaldu;
    }
    
    /**
     *
     * Wrapper for creating possible Canadian zip codes from digits
     *
     * @return array 
     */
    public function process()
    {
        $this->checkLength();
        $fs = $this->_getFS();
        $fsa = $this->_getFSA($fs);
        $fsal = $this->_getFSAL($fsa);
        $fsald = $this->_getFSALD($fsal);
        $fsaldu = $this->_getFSALDU($fsald);
        
        return $fsaldu;
    }
}