<?PHP
switch(empty($_GET['page']) ? '' : $_GET['page'] )
{
    case 'Sale':
    {
        $class      = 'Sale';
        break;
    }
    case 'Form':
    {
        $class      = 'Form';
        break;
    }
    case 'CreateRecurrentCard':
    {
        $class      = 'CreateRecurrentCard';
        break;
    }
    default:
    {
        $class      = 'Main';
    }
}

include_once './'.$class.'.php';

$Page               = new $class('http://'.SITE_HOST.'/index.php?page='.$class);
$Page->init();

// Simple dispatcher
if(isset($_POST['Process']))
{
    $Page->process_start();
}
elseif(isset($_POST['Update']))
{
    $Page->process_update();
}
elseif(isset($_GET['Redirect']))
{
    $Page->process_redirect($_POST);
}
else
{
    $Page->out_form();
}
