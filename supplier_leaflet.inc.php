<script type="text/javascript">
$(function(){
    var map = new ArcMap();
    var primaryCountry = [<?php echo $supplier_coordinates->longitude ?>, <?php echo $supplier_coordinates->latitude ?>];
    var coordinates = <?php echo json_encode($borrower_coordinates); ?>;

    map.drawAllPaths(primaryCountry, coordinates);
});

</script>
