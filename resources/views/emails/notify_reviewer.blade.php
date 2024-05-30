<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" />
</head>

<body>
    <div class="mail_card" style="border-radius: 1rem; width:43%; margin: 3rem auto; overflow: hidden;">
        <div class="header" style="padding: 1em 2.5rem; background: #cae0ff;">
            <img src="{{ config('app.url') }}/media/logos/logo_email.png" alt="" style="width: 180px;">
        </div>
        <div class="content" style=" padding: 1.5rem 2.5rem; background: #f6fcff;">
            
            <p>Dear <?php echo $data['name'] ?></p>
          
              <p><?php echo $data['msg'] ?>.</p>
            <p>Please check</p>
            <br />
            <p>Kindly click the below button to access the system.</p>
                <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td>
                            <a href="{{ config('app.url') }}"
                            class="button button-{{ $color ?? 'primary' }}" target="_blank" style="
                                        background-color: #26358b;
                                        border-color: #26358b;
                                        padding: 10px 30px;
                                        border-radius: 4px;
                                        color: #fff;
                                        font-size: 15px;
                                        font-weight: 600;
                                    ">View</a>
                        </td>
                    </tr>
                </table>
            <br />
        </div>
        <div class="footer" style="padding: 1em 2.5rem; background: #cae0ff;">
            {{ config('app.url') }}
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
</body>

</html>