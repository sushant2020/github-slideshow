<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" />

    <title>Hello, world!</title>
</head>

<body>
        
    <div class="mail_card" style="border-radius: 1rem; width:43%; margin: 3rem auto; overflow: hidden;">
        <div class="header" style="padding: 1em 2.5rem; background: #cae0ff;">
            <img src=" {{ config('app.url') }}/media/logos/logo_email.png" alt="" style="width: 180px;">
        </div>
        <div class="content" style=" padding: 1.5rem 2.5rem; background: #f6fcff;">            
            <p>Hello User,<p>
            <p>Below task is completed by <?php echo $data['completedBy']; ?> on <?php echo $data['completedAt']; ?></p>
            <br />
            <table style="border-collapse: collapse; min-width: 100%;">
                <tr>
                  <th style="border: 1px solid #c0c3d2; padding: 10px; font-weight:bold; width: 35%;">Product</th>
                  <th style="border: 1px solid #c0c3d2; padding: 10px; font-weight:bold; width: 35%;">Task</th>
                  <th style="border: 1px solid #c0c3d2; padding: 10px; font-weight:bold;width: 30%;">Assigned To</th>
                </tr>
                <tr>
                  <td style="border: 1px solid #c0c3d2; padding: 10px;"><?php echo $data['product'] . ' -'. $data['spot']; ?></td>
                  <td style="border: 1px solid #c0c3d2; padding: 10px;"><?php echo $data['task']; ?></td>
                  <td style="border: 1px solid #c0c3d2; padding: 10px;"><?php echo $data['assignedTo']; ?></td>
                </tr>
            </table>

            <br/>
            <p>Thank You</p>
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