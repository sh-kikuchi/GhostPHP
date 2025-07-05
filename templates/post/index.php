<?php include('templates/layouts/header.php'); ?>
<div class="ghost-wrapper px-2">
    <?php if (isset($errors)) : ?>
        <ul>
            <?php foreach($errors as $error) {?>
                <li class="text-center" style="list-style:none;"><?php echo h($error);?></li>
            <?php }?>
        </ul>
    <?php endif; ?>
    <h2 class="text-center pt-2">Posts</h2>
    <div class="text-right"><a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/post/create">create a new post</a></div>
    <div class="flex-box justify-center" style="height:80%; overflow-y:auto;">
        <table class="mx-3">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Body</th>
                    <th>Updated_at</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post){?>
                <tr>
                    <td class="text-center">
                      <?php echo h($post["title"]);?>
                    </td>
                    <td class="text-center">
                      <?php echo h($post["body"]);?>
                    </td>
                    <td class="text-center">
                      <?php echo h($post["updated_at"]);?>
                    </td>
                    <td class="text-center">
                        <div class="flex-box justify-center">
                            <div>
                                <a class="crud-edit" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/post/update?id=<?php echo h($post["id"]) ?>">EDIT</a>
                            </div>
                        </div>
                        <div>
                            <form name="post_delete"  method="POST" action="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/post/delete">
                                <input hidden name="csrf_token" value="<?php echo h($csrf); ?>">  
                                <input hidden name="id" value="<?php echo h($post["id"]); ?>">
                                <input hidden name="delete" value="">
                                <button type="submit" class="crud-delete">DELETE</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <!--pagenation-->
    <div class="flex-box justify-center my-2">
        <?php for($i = 1; $i <= $max_page; $i++){?>
            <a 
                class="pagenation"
                href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/post?page_id=<?php echo  $i; ?>">
                <?php echo $i ?>
            </a>
        <?php }?>
    </div>
</div>
<?php include('templates/layouts/footer.php'); ?>