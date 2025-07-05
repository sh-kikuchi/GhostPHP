<?php include('templates/layouts/header.php'); ?>

<div class="ghost-wrapper">
    <?php if (isset($errors)) : ?>
        <ul>
        <?php foreach($errors as $error) { ?>
            <li class="text-center" style="list-style:none;"><?php echo h($error); ?></li>
        <?php } ?>
        </ul>
    <?php endif; ?>
    <h2 class="text-center pt-2">Posts</h2>
    <a class="pl-3" href="<?php echo route("post"); ?>">BACK</a>
    <section class="flex-box justify-center">
        <form name="<?php echo isset($post) ? 'update_form' : 'create_form'; ?>" method="post" action="<?php echo isset($post) ? route('post/update') : route('post/create'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf); ?>"> 
            <input type="hidden" name="user_id" value="<?php echo h($signin_user['id']); ?>"> 
            <?php if (isset($post)) : ?>
                <input type="hidden" name="id" value="<?php echo h($post['id']); ?>">
            <?php endif; ?>
            <div class="flex-box">
                <label for="title" class="label">title</label>
                <input type="text" name="title" class="form-input" placeholder="100 words or less" value="<?php echo isset($old) ? h($old['title']) : (isset($post) ? h($post['title']) : ''); ?>">
            </div>
            <div class="flex-box">
                <label for="body" class="label">body</label>
                <input type="text" name="body" class="form-input" value="<?php echo isset($old) ? h($old['body']) : (isset($post) ? h($post['body']) : ''); ?>">
            </div>
            <div class="flex-box justify-center">
                <button type="submit" class="ghost-btn"><?php echo isset($post) ? "UPDATE" : "CREATE"; ?></button>
            </div>
        </form>
    </section>
</div>
<?php include('templates/layouts/footer.php'); ?>
