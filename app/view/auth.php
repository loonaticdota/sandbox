<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle; ?></title>
    <link rel="stylesheet" href="<?= BASEURL ?>vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
</head>
<body>
    
    <div class="section-1 p-4 vh-100 d-flex align-items-center justify-content-center">

        <div class="rounded-3 w-75 d-flex overflow-hidden" style="background-color: white; min-height: 200px; min-width: 600px; display: flex; align-items: center; justify-content: center; box-shadow: rgba(149, 157, 165, 0.5) 0px 8px 24px;">
        
            <div class="w-50 p-4" style="min-height: 200px;">

                    <div class="badge-officials my-3 d-flex align-items-center justify-content-center">

                        <img src="http://localhost/emaqra2026sulteng/assets/img/setting/1780113682Coat_of_arms_of_Central_Sulawesi.png" style="width:21%;height:auto;">
                        
                    </div>

                    <div class="form-group mb-3 px-4">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                    </div> 

                    <div class="form-group px-4 mb-3">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                    </div> 

                    <div class="form-group px-4 mb-3 d-flex align-items-center justify-content-center">
                        <button type="submit" class="btn rounded btn-primary mx-auto">Login</button>
                    </div>

            </div>
        

            <div class="w-50" style="min-height: 200px;"></div>

        </div>

    </div>


</body>
</html>