<?php
$imageUrls = [
    '/public/assets/img/architects_daugher.png',
    '/public/assets/img/microma.png',
    '/public/assets/img/special_elete.png',
];
$randomImage = $imageUrls[array_rand($imageUrls)];
?>

<?php include('templates/layouts/header.php'); ?>
    <div class="ghost-wrapper">
        <section class="py-2 flex-box justify-center align-center">
            <div>
                <img src="<?php echo dirname($_SERVER['SCRIPT_NAME']).$randomImage;?>"  style="width:400px"/>

                <div class="flex-box justify-center">
                    <a class="mr-1 ml-1" href="https://sh-revue.net/projects/ghostphp">Document</a>
                    <a class="mr-1 ml-1" href="https://github.com/sh-kikuchi/GhostPHP">GitHub</a>
                </div>

            </div>
        </section>
    </div>
<?php include('templates/layouts/footer.php'); ?>
