<script type="text/javascript">
$(function(){
    var map = new ArcMap();
    var primaryCountry = [<?php echo $borrower_cordinates["lng"] ?>, <?php echo $borrower_cordinates["lat"] ?>];
    var coordinates = <?php echo json_encode($supplier_coordinates); ?>;

    map.drawAllPaths(primaryCountry, coordinates);
});

</script>
