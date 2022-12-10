<?php

namespace BOGO;
?>

<style>
    #id01 {
        z-index: 999999999;
    }

    #id01 .w3-custom-color {
        background-color: #a1161a;
        padding: 10px 20px;
    }

    .w3-custom-color h2 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600 !important;
        color: #ffffff;
    }

    .w3-custom-color .w3-button {
        color: #ffffff;
        font-size: 30px;
    }

    .w3-custom-color .w3-button:hover {
        background: transparent !important;
        color: #ffffff !important;
    }

    .w3-container:last-child {
        padding: 20px !important;
    }

    .w3-container p {
        font-family: "Montserrat", Sans-serif;
        font-size: 16px;
        font-weight: 400;
    }

    @media only screen and (min-width:1000px) {
        .w3-modal-content {
            width: 100%;
            max-width: 700px;
        }
    }

    @media only screen and (max-width:768px) {
        .w3-modal {
            padding-top: 50%;
        }
    }
</style>



<!-- Trigger/Open the Modal -->
<div id="open-bogo" onclick="document.getElementById('id01').style.display='block'"></div>

<div id="id01" class="w3-modal">
    <div class="w3-modal-content">
<a href="/product-tag/bogo/" target="_blank"><img src="/wp-content/uploads/2022/12/extended-sale-popup.jpg" style="width:100%;max-width:700px;"></a>
       <!-- <header class="w3-container w3-custom-color">
            <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright">&times;</span>
            <h2>BUY 1 GET 1 FREE</h2>
        </header>

        <div class="w3-container">
            <p>Receive an additional accessory for <strong>FREE!</strong> </p>
            <p>Place one more accessory in your cart and receive it at no additional cost. </p>
            <p>Click <a target="_blank" href="/product-tag/bogo/">here</a> to see qualifying items.</p>
        </div>
-->
        <!-- <footer class="w3-container w3-teal">
      <p></p>
    </footer> -->

    </div>
</div>


<script>
    modal = document.getElementById('id01');

    window.onload = function() {
        document.getElementById('open-bogo').click();
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    jQuery(document.body).on('added_to_cart removed_from_cart', function(a, b, c, d){

        console.log(a);
        console.log(b);
        console.log(c);
        console.log(d);

    });
</script>