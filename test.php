
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<script>
    $('input[type="button"]').click(function(e){
        $(this).closest('tr').remove();
    })
    function removeRow(row) {
       // $(row).remove();
        $(row).closest('tr').remove();
    }
</script>

<?php 
include_once('Config/DB.php');

                    // Attempt select query execution
                    $sql = "SELECT * FROM Order_Product";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>referance</th>";
                                        echo "<th>quantite</th>";
                                        echo "<th></th>";
                                        echo "<th></th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['product_id'] . "</td>";
                                        echo "<td>" . $row['quantity'] . "</td>";
                                        echo "<td><input type='button' value='test'></td>";
                                        echo "<td><input class='btn btn-success' type='button' value='Allumer led'></td>";
                                        echo "<td><input onmousedown='removeRow(this);' class='btn btn-danger' type='button' value='Eteindre led'></td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
}}
echo "<table>
   <tr> 
       <td><input type='button' value='1'></td>
   </tr>
   <tr> 
       <td><input type='button' value='2' ></td>
   </tr>
   <tr> 
       <td><input type='button' value='3'></td>
   </tr>
</table>";

?>
<table>
   <tr> 
       <td><input type="button" value=""></td>
   </tr>
   <tr> 
       <td><input type="button" value="" ></td>
   </tr>
   <tr> 
       <td><input type="button" value=""></td>
   </tr>
</table>