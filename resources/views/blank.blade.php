<style>
    @import url(https://fonts.googleapis.com/css?family=Raleway:700);

    *,
    *:before,
    *:after {
        box-sizing: border-box;
    }

    html {
        height: 100%;
    }

    body {
        font-family: 'Raleway', sans-serif;
        background-color: #342643;
        height: 100%;
        padding: 10px;
    }

    a {
        color: #EE4B5E !important;
        text-decoration: none;
    }

    a:hover {
        color: #FFFFFF !important;
        text-decoration: none;
    }

    .text-wrapper {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .title {
        font-size: 5em;
        font-weight: 700;
        color: #EE4B5E;
    }

    .subtitle {
        font-size: 40px;
        font-weight: 700;
        color: #1FA9D6;
    }

    .isi {
        font-size: 18px;
        text-align: center;
        margin: 30px;
        padding: 20px;
        color: white;
    }

    .buttons {
        margin: 30px;
        font-weight: 700;
        border: 2px solid #EE4B5E;
        text-decoration: none;
        padding: 15px;
        text-transform: uppercase;
        color: #EE4B5E;
        border-radius: 26px;
        transition: all 0.2s ease-in-out;
        display: inline-block;

        .buttons:hover {
            background-color: #EE4B5E;
            color: white;
            transition: all 0.2s ease-in-out;
        }
    }
    .barcode-container {
        /* background: #f0f0f0; /* Set your desired background color or image here */
        /* padding: 20px; Adjust padding as needed */ */
        text-align: center; /* Center the content horizontally */
    }

    .barcode-container img {
        max-width: 100%; /* Make sure the barcode image doesn't exceed the container width */
        height: auto; /* Maintain the aspect ratio of the image */
        filter: invert(100%);
    }
</style>
<div class="text-wrapper">
    <div class="title" data-content="404">
        MAAF
    </div>


    <div class="subtitle">
        Anda belum terdaftar di sistem telegram kami
    </div>
    <div class="isi" style="text-align: center">
        Lengkapi pendaftaran dengan scan QR berikut <br>
        <hr>
        <div class="barcode-container ">
            <img src="data:image/png;base64,{{DNS2D::getBarcodePNG('https://t.me/gading_adv_bot', 'QRCODE')}}" alt=""> <br> <br>
            atau klik tombol di bawah ini
        </div>
    </div>

    <div class="buttons">
        <a class="button" href="https://t.me/gading_adv_bot" target="blank">GAding ADV BOT</a>
    </div>
</div>
