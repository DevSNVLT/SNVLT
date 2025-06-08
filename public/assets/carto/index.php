<?php
$connexion = new PDO('pgsql:host=localhost;port=5433;dbname=dpif', 'postgres','020780');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PEF DPIF</title>

    <link rel="stylesheet" href="resources/ol/ol.css">
    <link rel="stylesheet" href="resources/layerswitcher/ol-layerswitcher.css">

    <link rel="stylesheet" href="resources/fontawsome/css/all.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link rel="stylesheet" href="assets/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/style.css">

    <link rel="stylesheet" href="js/css/bootstrap.min.css">
    <link rel="stylesheet" href="table/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="table/dist/extensions/filter-control/bootstrap-table-filter-control.min.css">
    <script src="assets/dselect/dist/js/dselect.js"></script>


    <link rel="shortcut icon " href="resources\images\map.png">
</head>

<body class="text-dark" style="background:lightgray">
<br><div style="margin-left:2%;">
		<span class="badge badge-primary text-light"><h4 class="text-light"> Arbres coupés :
			<?php
            $con = new PDO('pgsql:host=localhost;port=5433;dbname=dpif', 'postgres', '020780');
            $query = "SELECT COUNT(*) AS nb_arbres FROM deif.lignepagebrh WHERE lignepagebrh.lettre_lignepagebrh='A'";

            $result = $con->prepare($query);
            $result->execute();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                <?php echo $row['nb_arbres']; ?>
                <?php
            }
            ?>
		</h4></span>
    <span class="badge badge-success text-light"><h4 class="text-light"> Grumes :
			<?php
            $con = new PDO('pgsql:host=localhost;port=5433;dbname=dpif', 'postgres', '020780');
            $query = "SELECT COUNT(*) AS nb_billes FROM deif.lignepagebrh";

            $result = $con->prepare($query);
            $result->execute();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                <?php echo $row['nb_billes']; ?>
                <?php
            }
            ?>
		</h4></span>
    <span class="badge badge-warning text-light"><h4 class="text-light"> Cubage :
			<?php
            $con = new PDO('pgsql:host=localhost;port=5433;dbname=dpif', 'postgres', '020780');
            $query = "SELECT Round(SUM(cubage/1000)) AS cubage FROM deif.lignepagebrh";

            $result = $con->prepare($query);
            $result->execute();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                <?php echo $row['cubage']." m3"; ?>
                <?php
            }
            ?>
		</h4></span>
</div>
<br>
<div id="map" class="bg-light">

    <button id="btnCrosshair" title="Live Location">
        <i class="fa fa-crosshairs fa-2x"></i>
    </button>

</div>

<div id="popup" class="ol-popup">
    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
    <div id="popup-content"></div>
</div>
<div id="layersDiv" class="layersDiv">
    <div class="headerDiv" id="headerDiv">
        <label for="">Couches</label>
    </div>
    <div id="layerSwitcherContent" class="layer-switcher"></div>
</div>
<!-- <div class="toggleAttQueryDiv" id="toggleAttQueryDiv"></div> -->
<div class="attQueryDiv" id="attQueryDiv">
    <div class="headerDiv" id="headerDiv">
        <label for="">Requêtes</label>
    </div>
    <!-- <br> -->
    <label for="">Selectionner une couche</label>
    <select name="selectLayer" id="selectLayer" class="form-control">
    </select>
    <!-- <br><br> -->

    <label for="">Selectionnez un attribut</label>
    <select name="selectAttribute" id="selectAttribute" class="form-control">
    </select>
    <!-- <br><br> -->

    <label for="">Selectionnez un opérateur</label>
    <select name="selectOperator" id="selectOperator" class="form-control">
    </select>
    <!-- <br><br> -->

    <label for="">Entrez une valeur</label>
    <input type="text" name="enterValue" id="enterValue" class="form-control">
    </select>
    <!-- <br><br> -->

    <button type="button" id="attQryRun" class="btn btn-success">Exécuter</button>
    <button type="button" id="attQryClear" class="btn btn-danger">Effacer</button>

</div>
<!-- <div class="toggleAttributeListDiv" id="toggleAttributeListDiv"></div> -->
<div class="attListDiv" id="attListDiv">
</div>

<div class="spQueryDiv" id="spQueryDiv">
    <div class="headerDiv" id="headerDiv">
        <label for="">Requêtes spatiales</label>
    </div>
    <label for="">Selectionnez les entités </label>
    <select name="buffSelectLayer" id="buffSelectLayer" class="form-control">
    </select>
    <!-- <br><br> -->

    <label for="">qui sont </label>
    <select name="qryType" id="qryType" class="form-control">
        <option value="withinDistance">a une distance de</option>
        <option value="intersecting">tintersectent</option>
        <option value="completelyWithin">Completement dans </option>
    </select>
    <!-- <br><br> -->

    <div class="bufferDiv" id="bufferDiv">
        <!-- <label for="">Distnace in meter</label> -->
        <input type="number" name="bufferDistance" id="bufferDistance" placeholder="1000" class="form-control">
        <select name="distanceUnits" id="distanceUnits" class="form-control">
            <option value="meters">Mètres</option>
            <option value="kilometers">kilomètres/option>
            <option value="feet">Pieds</option>
            <option value="nautical miles">Nautiques</option>
        </select>
        <!-- <br><br> -->

        <label for="">de</label>
    </div>


    <select name="srcCriteria" id="srcCriteria" class="form-control">
        <option value="pointMarker">Point</option>
        <option value="lineMarker">Ligne</option>
        <option value="polygonMarker">Polygone</option>
    </select>
    <!-- <br><br> -->

    <button type="button" id="spUserInput" class="spUserInput"><img src="resources/images/selection.png" alt=""
                                                                    style="width:17px;height:17px;vertical-align:middle"></img></button>

    <button type="button" id="spQryRun" class="btn btn-success">Exécuter</button>

    <button type="button" id="spQryClear" class="btn btn-danger">Effacer</button>
</div>

<div class="editingControlsDiv" id="editingControlsDiv">

</div>

<div class="settingsDiv" id="settingsDiv">
    <div class="headerDiv" id="headerDiv">
        <label for="">Configuration</label>
    </div>
    <label for="">Info Entités</label><br>
    <select name="featureInfoLayer" id="featureInfoLayer" class="form-control">
        <option value="All layers">Toutes les couches</option>
        <option value="Visible layers">Couches visibles</option>
    </select>
    <label for="">Edition de couches</label><br>
    <select name="editingLayer" id="editingLayer" class="form-control">
    </select>
</div>
<div class="attQueryDiv" id="projectsDiv">
    <div class="headerDiv" id="headerDiv">
        <label for="">Projections</label>
    </div>
    <label for="">PEF</label><br>
    <select name="pefLayer" id="pefLayer" class="form-control">
        <option></option>
        <?php
        $con = new PDO('pgsql:host=localhost;port=5433;dbname=dpif', 'postgres', '020780');
        $query = "SELECT * FROM deif.perimetrenu WHERE perimetrenu.denomination='PEF' ORDER BY perimetrenu.numero_perimetrenu ASC";

        $result = $con->prepare($query);
        $result->execute();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
            <option><?php echo $row['numero_perimetrenu']; ?></option>
            <?php
        }
        ?>
    </select>


    <label for="">Date de début</label>
    <input type="date" name="date1" id="date1" placeholder="dd-mm-yyyy" class="form-control">
    </br>
    <label for="">Date de fin</label>
    <input type="date" name="date2" id="date2" placeholder="dd-mm-yyyy" class="form-control">
    </br>
    <button type="button" id="attQryRunP" name="attQryRunP" >Valider</button>
    <button type="button" id="PefClear" class="btn btn-danger">Effacer</button>
</div>

<div id="legende" style="color:grey;background-color:white;margin-left:20px;">
    <u><h4><span></span>Légende:</h4></u>
    <br>
    <div style="color:grey;background-color:white;margin-left:20px;">
        <h4>Zones:</h4>
        <img src="http://localhost:8085/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=DPIF:pef&STYLE=style_pef">
        <br>
        <br>
    </div>
    <div style="color:grey;background-color:white;margin-left:20px;">
        <h4>Arbres abattus:</h4>
        <img src="http://localhost:8085/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=DPIF:all_pef_arbres&STYLE=arbres_pef">
        <br>
        <br>
    </div>
    <div style="color:grey;background-color:white;margin-left:20px;">
        <h4>Volumes exploités (m3):</h4>
        <img src="http://localhost:8085/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=DPIF:all_pef_cubage&STYLE=voume_pef">
        <br>
        <br>
    </div>
</div>

<div class="cntr" id="cntr">
    <div class="cntr-innr">
        <label class="search" for="inpt_search">
            <input id="inpt_search" type="text" />
        </label>
    </div>
    <div class="liveDataDiv" id="liveDataDiv"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.4.3/proj4.js"></script>
<script src="resources/ol/ol.js"></script>
<script src="resources/layerswitcher/ol-layerswitcher.js"></script>
<script src="resources/jQuery/jquery-3.6.0.min.js"></script>
<script src="resources/fontawsome/js/all.js"></script>
<script src="main.js"></script>
</body>

</html>