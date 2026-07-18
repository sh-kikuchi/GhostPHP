<?php include('templates/layouts/header.php'); ?>

<div class="ghost-wrapper">
    <?php if (!empty($errors)) : ?>
        <ul>
            <?php foreach ($errors as $error) { ?>
                <?php foreach ($error as $message) { ?>
                    <li class="text-center" style="list-style:none;">
                        <?= h($message) ?>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    <?php endif; ?>

    <h2 class="text-center pt-2">Posts</h2>

    <a class="pl-3" href="<?= route("post") ?>">BACK</a>

    <section class="flex-box justify-center">

        <form
            name="<?= isset($post) ? 'update_form' : 'create_form' ?>"
            method="post"
            action="<?= isset($post) ? route('post/update') : route('post/create') ?>"
        >

            <input type="hidden" name="csrf_token" value="<?= h($csrf ?? '') ?>">
            <input type="hidden" name="user_id" value="<?= h($signin_user['id'] ?? '') ?>">

            <?php if (!empty($post)) : ?>
                <input type="hidden" name="id" value="<?= h($post['id'] ?? '') ?>">
            <?php endif; ?>

            <?php
                $title = $old['title'] ?? ($post['title'] ?? '');
                $body  = $old['body'] ?? ($post['body'] ?? '');
            ?>

            <div class="flex-box">
                <label for="title" class="label">title</label>
                <input type="text"
                       name="title"
                       class="form-input"
                       placeholder="100 words or less"
                       value="<?= h($title) ?>">
            </div>

            <div class="flex-box">
                <label for="body" class="label">body</label>
                <input type="text"
                       name="body"
                       class="form-input"
                       value="<?= h($body) ?>">
            </div>

            <div class="flex-box justify-center">
                <button type="submit" class="ghost-btn">
                    <?= isset($post) ? "UPDATE" : "CREATE" ?>
                </button>
            </div>

        </form>
    </section>
</div>

<?php include('templates/layouts/footer.php'); ?>