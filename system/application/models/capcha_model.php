<?
/**
 * Captcha model
 *
 * Interacts with the captcha table and creates and validates
 * the captcha using the captcha plugin
 *
 * @package		openviz
 * @subpackage          admin
 * @author		Robert Tooker
 * @since		Version 1.0
 */

class Capcha_model extends Model {
 private  $_model_table = 'captcha';
    /**
     * Defines the table for the model, used for active record functions
     * @access private
     * @var string
     */
 

    /**
     * How long images and database records will be retained
     * @access private
     * @var integer
     */
    private $_expiration = 7200;

    /**
     * Auto increment field, primary key
     * @access public
     * @var integer
     */
    public $id = null;

    /**
     * The time the captcha was created (unix timestamp)
     * @access public
     * @var integer
     */
    public $captcha_time = 0;

    /**
     * The IP address of the user when the captcha was created
     * @access public
     * @var text
     */
    public $ip_address = '';

    /**
     * The word shown in the captcha image
     * @access public
     * @var text
     */
    public $word = '';

    /**
     * Constructor
     */
    function __construct() {
        parent::Model();
    }

    /**
     * Makes a captcha image
     * @param text $ip_address the IP address of the user
     * @return text a link to the image or an error message if image could not be created
     */
    function make($ip_address) {
        $this->load->plugin( 'captcha' );


        $parameters = array(
            'img_path' => './userfiles/captcha/',
            'img_url' => base_url() . 'userfiles/captcha/',
            'img_width' => 200,
            'img_height' => 50,
            'expiration' => $this->_expiration
        );

        $captcha = create_captcha( $parameters );
        if ( $captcha ) {
            $this->id = null;
            $this->captcha_time = $captcha['time'];
            $this->ip_address = $ip_address;
            $this->word = $captcha['word'];

            $this->db->insert($this->_model_table, $this->model_to_array());
            $this->id = $this->db->insert_id();
        } else {
            return "Could not make captcha." ;
        }
        return $captcha['image'] ;
    }

    /**
     * Checks the response to a captcha image
     * @param text $ip_address the ip address of the response
     * @param text $response the response which should match the word
     * @return bool if the response is correct or not
     */
    function check($ip_address, $response) {
        $this->db->where('captcha_time <', time() - $this->_expiration);
        $this->db->delete($this->_model_table);

        //checking input
        $this->db->where('ip_address', $ip_address);
        $this->db->where('word', $response);
        $query = $this->db->get($this->_model_table);

        if ( $query->num_rows() > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Assigns the values of a captcha array to the captcha model
     *
     * Receives an array and sets matching properties of the model, leaving
     * unmatched properties intact. Useful for receiving a subset of fields
     * from a page post and applying an update using active record.
     *
     * @access public
     * @param mixed $data an associative array of datafield properties with the key representing the property name
     */
    function array_to_model($data) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['captcha_time'])) $this->captcha_time = $data['captcha_time'];
        if (isset($data['ip_address'])) $this->id = $data['ip_address'];
        if (isset($data['word'])) $this->id = $data['word'];
    }

    /**
     * Returns the model's database fields as an associative array
     *
     * @access public
     * @return mixed an associative array of datafield properties with the key representing the property name
     */
    function model_to_array() {
        $data['id'] = $this->id;
        $data['captcha_time'] = $this->captcha_time;
        $data['ip_address'] = $this->ip_address;
        $data['word'] = $this->word;
        return $data;
    }

}

?>