<?php                          
$val = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGDEB</title>
	
  <link href="../bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../../resources/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

	<link rel="stylesheet" href="resources/leaflet/leaflet.css">

   <link rel="stylesheet" href="resources/libs/leaflet-measure.css">
   <link rel="stylesheet" href="resources/Leaflet.PolylineMeasure.css">
   <link rel="stylesheet" href="style.css">
   <link rel="stylesheet" href="main.css">
   
	<script type="text/javascript" src="../xlsx.full.min.js"></script>

    

</head>
<body>
    <div class="container" style="width:100%;height:50px;">
    
    <label for="" class="lbl">Requêtes attributaires</label> 
        <select name="cmb_citere" id="cmb_citere" class="combo-list" editable=true>
            <option value="5"></option>
            <option value="1">Nationalité</option>
            <option value="2">Etat</option>
            <option value="3">Type Agrément</option>
            <option value="3">Quartier</option>
            <option value="4">Code GPS</option>
        </select>
        <input type="text" name="txt-critere" id="txt-critere" class="combo-list" placeholder="valeur...">
        
      <button onclick="rechercheValeurs();"  class = "btn"  id="btn_citere">Rechercher</button>
      <a href="#editEmployeeModal" data-toggle="modal">Afficher les valeurs</a>
      
      </div>
    
    <div class="container" style="width:100%">
        
        <div class="office-list">
            <div class="heading">
                <h2>Dépôts Yopougon</h2>
            </div>
            <input type="search" placeholder="Rechercher..."  id="search-input" data-table="list" onkeyup="myFunction()" style="font-family:arial;font-size:16px;height:35px;margin: 5px 5px 5px 0px;width:100%;"/>
            <ul class="list" id="list"></ul>
        </div>
        <div id="map"></div>
    </div>

    <script>
        function myFunction() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("search-input");
            filter = input.value.toUpperCase();
            ul = document.getElementById("list");
            li = ul.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                p = li[i].getElementsByTagName("p")[0];
                txtValue = a.textContent || a.innerText  ;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
        </script>
   
    <script src="resources/leaflet/leaflet.js"></script>
    <script src="resources/libs/leaflet.browser.print.js"></script>
     <!--Leaflet Providers-->
   <script src="resources/jquery/jquery-3.6.0.min.js"></script>
   <script src="resources/jquery/jquery-3.4.1.js"></script>
   <!--Leaflet Providers-->
   <script src="resources/leaflet-providers.js"></script>

    <!--Leaflet Unité de mesure-->
    <script src="resources/Leaflet.PolylineMeasure.js"></script>
    
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="resources/libs/leaflet-measure.js"></script>
    <script src="resources/data/depots.js"></script>
    <script src="main.js"></script>



    
    <style type="text/css">
body {
color: #566787;
background: #f5f5f5;
font-family: 'Varela Round', sans-serif;
font-size: 13px;
}
.table-wrapper {
background: #fff;
padding: 20px 25px;
margin: 30px 0;
border-radius: 3px;
box-shadow: 0 1px 1px rgba(0,0,0,.05);
}
.table-title {
padding-bottom: 15px;
background: #435d7d;
color: #fff;
padding: 16px 30px;
margin: -20px -25px 10px;
border-radius: 3px 3px 0 0;
}
.table-title h2 {
margin: 5px 0 0;
font-size: 24px;
}
.table-title .btn-group {
float: right;
}
.table-title .btn {
color: #fff;
float: right;
font-size: 13px;
border: none;
min-width: 50px;
border-radius: 2px;
border: none;
outline: none !important;
margin-left: 10px;
}
.table-title .btn i {
float: left;
font-size: 21px;
margin-right: 5px;
}
.table-title .btn span {
float: left;
margin-top: 2px;
}
table.table tr th, table.table tr td {
border-color: #e9e9e9;
padding: 12px 15px;
vertical-align: middle;
}
table.table tr th:first-child {
width: 60px;
}
table.table tr th:last-child {
width: 100px;
}
table.table-striped tbody tr:nth-of-type(odd) {
background-color: #fcfcfc;
}
table.table-striped.table-hover tbody tr:hover {
background: #f5f5f5;
}
table.table th i {
font-size: 13px;
margin: 0 5px;
cursor: pointer;
}
table.table td:last-child i {
opacity: 0.9;
font-size: 22px;
margin: 0 5px;
}
table.table td a {
font-weight: bold;
color: #566787;
display: inline-block;
text-decoration: none;
outline: none !important;
}
table.table td a:hover {
color: #2196F3;
}
table.table td a.edit {
color: #FFC107;
}
table.table td a.delete {
color: #F44336;
}
table.table td i {
font-size: 19px;
}
table.table .avatar {
border-radius: 50%;
vertical-align: middle;
margin-right: 10px;
}
.pagination {
float: right;
margin: 0 0 5px;
}
.pagination li a {
border: none;
font-size: 13px;
min-width: 30px;
min-height: 30px;
color: #999;
margin: 0 2px;
line-height: 30px;
border-radius: 2px !important;
text-align: center;
padding: 0 6px;
}
.pagination li a:hover {
color: #666;
}
.pagination li.active a, .pagination li.active a.page-link {
background: #03A9F4;
}
.pagination li.active a:hover {
background: #0397d6;
}
.pagination li.disabled i {
color: #ccc;
}
.pagination li i {
font-size: 16px;
padding-top: 6px
}
.hint-text {
float: left;
margin-top: 10px;
font-size: 13px;
}
/* Custom checkbox */
.custom-checkbox {
position: relative;
}
.custom-checkbox input[type="checkbox"] {
opacity: 0;
position: absolute;
margin: 5px 0 0 3px;
z-index: 9;
}
.custom-checkbox label:before{
width: 18px;
height: 18px;
}
.custom-checkbox label:before {
content: '';
margin-right: 10px;
display: inline-block;
vertical-align: text-top;
background: white;
border: 1px solid #bbb;
border-radius: 2px;
box-sizing: border-box;
z-index: 2;
}
.custom-checkbox input[type="checkbox"]:checked + label:after {
content: '';
position: absolute;
left: 6px;
top: 3px;
width: 6px;
height: 11px;
border: solid #000;
border-width: 0 3px 3px 0;
transform: inherit;
z-index: 3;
transform: rotateZ(45deg);
}
.custom-checkbox input[type="checkbox"]:checked + label:before {
border-color: #03A9F4;
background: #03A9F4;
}
.custom-checkbox input[type="checkbox"]:checked + label:after {
border-color: #fff;
}
.custom-checkbox input[type="checkbox"]:disabled + label:before {
color: #b8b8b8;
cursor: auto;
box-shadow: none;
background: #ddd;
}
/* Modal styles */
.modal .modal-dialog {
max-width: 400px;
}
.modal .modal-header, .modal .modal-body, .modal .modal-footer {
padding: 20px 30px;
}
.modal .modal-content {
border-radius: 3px;
}
.modal .modal-footer {
background: #ecf0f1;
border-radius: 0 0 3px 3px;
}
.modal .modal-title {
display: inline-block;
}
.modal .form-control {
border-radius: 2px;
box-shadow: none;
border-color: #dddddd;
}
.modal textarea.form-control {
resize: vertical;
}
.modal .btn {
border-radius: 2px;
min-width: 100px;
}
.modal form label {
font-weight: normal;
}
</style>

        </head>
        <body>
        
        <
        
        <!-- Trigger/Open The Modal -->
        <?php $id = 0;?>
        
        <!-- The Modal -->
        
        
          <!-- Modal content -->
          <div id="editEmployeeModal" class="modal fade" style="overflow-y:scroll;">
          <input type="search" placeholder="Rechercher..." class="form-control search-input" data-table="customers-list" style="margin-top:5px;margin-left:200px;width:300px;"/>
          <button onclick=""  class = "btn"  id="btn_export">Exporter</button>
          <div class="card-body">
            <table class="table table-striped mt32 customers-list table-hover" id="tbldepots" style="margin-top: 10px;background:white;margin-left:10px;width:80%;overflow-y:scroll;">
                                    <thead>
                                        <tr>
                                            <td>Id</td>
                                            <td>Numéro Dossier</td>
                                            <td>Permis</td>
                                            <td>Nom</td>
                                            <td>Prénoms</td>
                                            <td>Contact</td>
                                            <td>Nationalité</td>
                                            <td>Quartier</td>
                                            <td>Type Agrément</td>
                                            <td>Etat</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                                    
                                                    $con = new PDO('pgsql:host=localhost;port=5432;dbname=depots', 'postgres', '020780');
                                                   
                                                   
                                                   echo $val;
                                                   $valeur = "%Banco%";
                                                        $query = "SELECT * FROM public.depots WHERE quartier LIKE '". $valeur  . "' ORDER BY code ASC ;";
                                                        //echo $query;
                                                        $result = $con->prepare($query);
                                                             $result  ->execute();
        
                                                         while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                            $id = $id + 1;
                                                ?>
                                                <tr>
                                                    
                                                    <td><?= $row['code']; ?></td>
                                                    <td><?= $row['numero_depot']; ?></td>
                                                    <td><?= $row['numero_per']; ?></td>
                                                    <td><?= $row['nom']; ?></td>
                                                    <td><?= $row['prenoms']; ?></td>
                                                    <td><?= $row['contact']; ?></td>
                                                    <td><?= $row['nationalit']; ?></td>
                                                    <td><?= $row['quartier']; ?></td>
                                                    <td><?= $row['type_d_agr']; ?></td>
                                                    <td><?= $row['etat_activ']; ?></td>
                                                    <?php
                                                         }
                                                         ?>
                                                         <div style="background:white;color:red;font-weight:bold;font-size:16px;margin-bottom:10px;"><?php echo $id;?> dépôts</div>
                                    </tbody>
                                </table>
                                <script>
        (function(document) {
            'use strict';

            var TableFilter = (function(myArray) {
                var search_input;

                function _onInputSearch(e) {
                    search_input = e.target;
                    var tables = document.getElementsByClassName(search_input.getAttribute('data-table'));
                    myArray.forEach.call(tables, function(table) {
                        myArray.forEach.call(table.tBodies, function(tbody) {
                            myArray.forEach.call(tbody.rows, function(row) {
                                var text_content = row.textContent.toLowerCase();
                                var search_val = search_input.value.toLowerCase();
                                row.style.display = text_content.indexOf(search_val) > -1 ? '' : 'none';
                            });
                        });
                    });
                }

                return {
                    init: function() {
                        var inputs = document.getElementsByClassName('search-input');
                        myArray.forEach.call(inputs, function(input) {
                            input.oninput = _onInputSearch;
                        });
                    }
                };
            })(Array.prototype);

            document.addEventListener('readystatechange', function() {
                if (document.readyState === 'complete') {
                    TableFilter.init();
                }
            });

        })(document);
    </script>
	
	
                                </div>
        </div>
		<script>

    function html_table_to_excel(type)
    {
        var data = document.getElementById('tbldepots');

        var file = XLSX.utils.table_to_book(data, {sheet: "sheet1"});

        XLSX.write(file, { bookType: type, bookSST: true, type: 'base64' });

        XLSX.writeFile(file, 'liste dépots.' + type);
    }

    const export_button1 = document.getElementById('btn-export');

    export_button1.addEventListener('click', () =>  {
        html_table_to_excel('xlsx');
    });

</script>
</body>
<?php 
?>
</html>