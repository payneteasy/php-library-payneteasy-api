<?PHP
class Template
{
    public $title       = '';
    public $content     = '';

    public function menu()
    {
        $page           = empty($_GET['page']) ? '' : $_GET['page'];

        $menu           = array();

        if(empty($page))
        {
            $menu[]     = '<li class="active"><a href="/index.php">Index</a></li>';
        }
        else
        {
            $menu[]     = '<li><a href="/index.php">Index</a></li>';
        }

        foreach(array('Sale', 'Form', 'CreateRecurrentCard') as $value)
        {
            if($value == $page)
            {
                $menu[]     = '<li class="active"><a href="/index.php?page='.$value.'">'.$value.'</a></li>';
            }
            else
            {
                $menu[]     = '<li><a href="/index.php?page='.$value.'">'.$value.'</a></li>';
            }
        }

        return implode('', $menu);
    }

    public function out()
    {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Paynet integration samples <?=$this->title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="http://wiki.payneteasy.com/">Paynet Easy</a>
          <div class="nav-collapse">
            <ul class="nav">
                <?=$this->menu()?>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
      <h1>Paynet integration samples</h1>
      <?=$this->content?>
    </div> <!-- /container -->
  </body>
</html>
<?PHP
    }
}
