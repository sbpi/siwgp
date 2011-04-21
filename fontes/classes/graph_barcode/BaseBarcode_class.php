<?PHP

abstract class BaseBarcode {
    //

    protected $name;
    protected $value;
    //

    protected $factor;   // size Factor
    protected $height;   // height of bars
    protected $width;    // width of barcode plus borders
    protected $divStyle; // outer div style
    //

    protected $symbolSet;
    // output
    protected $html; //
    protected $style; // output
    //
    //==========================================================================
    // Constructors
    public function __construct() {
        //
        $this->height   = 0;
        $this->width    = 0;
        $this->factor   = 1;
    } // end of constructor
    //--------------------------------------------------------------------------
    public function exec(){

        $this->genStyle();
        $this->genHTML();

        switch (func_num_args()){
            case 0:
                break;
            case 1:
                $app = func_get_arg(0); // a referance to a app instance
                $app->addHTML($this->html);
                $app->addStyle($this->style);
                break;
            default:
                die ("wrong number of params");
        }
    }
    //==========================================================================
    public function getStyle()                 {return $this->style;}
    public function getHTML()                  {return $this->html;}
    public function getBarcode(){
        // this is for standalone apps
        $this->exec();
        return $this->style . $this->html;
    }
    //==========================================================================
    abstract function genStyle();
    abstract function genHTML();
    abstract function getWidth();
    abstract function setFactor($factor);
    //--------------------------------------------------------------------------
    public function setName($name)           {$this->name      = $name;}
    public function setValue($value)         {$this->value     = $value;}
    public function setHeight($height)       {$this->height    = $height;}

    public function setNarrow($narrow)       {$this->narrow    = $narrow;}

    public function addStyle($divStyle)      {$this->divStyle  = $divStyle;}
} // end of class
//==============================================================================
?>