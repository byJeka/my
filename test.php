<?php
session_start();
if (isset ($_POST[action])) {
    if (($_POST[action]) == "delete") {
        $csv = new CSV("test.csv");
        $csv->delCSV($_POST[data]);
        unset($_SESSION['error']);
    } else {
        $_SESSION["error"]=true;
    }
}
class CSV
{
    private $csv_file = null;

    function __construct($csv_file)
    {
        if (file_exists($csv_file)) {
            $this->_csv_file = $csv_file;
        } else {
            throw new Exception("Файл " . $csv_file . " не найден");
        }
    }

    function getCSV()
    {
        $handle = fopen($this->_csv_file, "r");
        $array_line_full = array();
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            $array_line_full[] = $line;
        }
        fclose($handle);
        return $array_line_full;
    }

    function delCSV($id)
    {
        $ar = $this->getCSV();
        $handle = fopen($this->_csv_file, "w+");
        fclose($handle);
        $handle = fopen($this->_csv_file, "a");
        foreach ($ar as $value) {
            $row_id = $value[0];
            $row_val = $value;
            print_r("row_id =  ".$row_id);
            foreach ($id as $value) {
                print_r("value =  ".$value);
                if ($row_id == $value) {
                    $z=1;
                }
            }if($z!=1) {
                fputcsv($handle, $row_val, ";");
            }else{
                $z=0;
            }
        }
        fclose($handle);
    }
}
?>

<script type="text/javascript" src="jquery-2.1.3.min.js"></script>
<script type="text/javascript">
    var checked_array = new Array();
    $(document).ready(function(){
        jQuery(".chk").change(function(){
            get_checked();
            if (checked_array.length > 0){
                jQuery("#btn").attr("disabled", false);
            }else{
                jQuery("#btn").attr("disabled", true);
            }
        });
        jQuery("#btn").click(function(){
            if (confirmDelete()) {
                jQuery.post( "test.php",{action: "delete", data:checked_array}, onAccess)
            }
        });
    });
    function onAccess(data){
        window.location.replace("test.php");//редирект
    }

    function get_checked(){
        checked_array.length = 0;
        jQuery(".chk").each(function(i,elem) {
            id = jQuery(elem).attr('id');
            if(jQuery("#"+id).prop("checked")==true){
                checked_array.push(id);
            }
        });
    }

    function confirmDelete() {
        if (confirm("Вы подтверждаете удаление выбраных елементов?")) {
            return true;
        } else {
            return false;
        }
    }
</script>
<body>
        <?php
        if($_SESSION["error"]){
            echo '<h1>Ошибка, поробуйте еще раз</h1>';
        }
            try {
                $csv = new CSV("test.csv");
                $get_csv = $csv->getCSV();
                echo '<table border="1">';
                echo '<caption>СПИСОК ЭЛЕМЕНТОВ</caption>';
                foreach ($get_csv as $value) {
                    echo '<tr><td><input type="checkbox" class="chk" id="'.$value[0].'"/></td><td>'. $value[1] .'</td>';
                }
                echo '</table>';
                echo '<button id="btn" type="submit" disabled="disabled" style="margin: 5px">Удалить</button>';
                }
            catch (Exception $e) {
                echo "Ошибка: " . $e->getMessage();
            }
        ?>
</body>