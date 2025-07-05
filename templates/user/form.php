<?php include('templates/layouts/header.php') ?>
<div class="ghost-wrapper">
    <?php if (isset($errors)) : ?>
      <ul>
      <?php foreach($errors as $error) {?>
        <li class="text-center" style="list-style:none;"><?php echo h($error);?></li>
      <?php }?>
      </ul>
    <?php endif; ?>
    <h2 class="text-center pt-2"> <?php echo $form_name === 'signup' ? "SIGN UP" : "SIGN IN";?></h2>
    <section class="flex-box justify-center">
        <form name = <?php echo $form_name ?> method="post" action="<?php echo $form_name === 'signup'
          ? route('signup') 
          : route('signin') ?>"
        >
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf); ?>">
            <div class="input-area">
                <!-- username -->  
                <?php if($form_name === 'signup'): ?>
                  <div class="flex-box justify-center my-2">
                    <label for="name" class="label">Username：</label>
                    <input type="text" name="name" class="form-input" value="<?php if($old){echo h($old['name']);}"}"?>">
                  </div>
                <?php endif; ?>
                <!-- email -->
                <div class="flex-box justify-center">
                    <label for="email" class="label">E-mail:</label>
                    <input type="email" name="email" class="form-input" value="<?php if($old){echo h($old['email']);}"}"?>">
                </div>
                <!-- password -->
                <div class="flex-box justify-center">
                  <label for="password" class="label">Password：</label>
                  <input type="password" name="password" class="form-input" value="<?php if($old){echo h($old['password']);}"}"?>">
                </div>
                <!-- pass-confirm -->
                <?php if($form_name === 'signup'): ?>
                    <div class="flex-box justify-center my-2">
                    <label for="password_conf" class="label">Pass-Confirm：</label>
                    <input type="password" name="password_conf" class="form-input" value="<?php if($old){echo h($old['password_conf']);}"}"?>">
                </div>
                <?php endif; ?>
            </div>
          <!-- submit -->
          <div class="submit-area flex-box justify-center my-2">
              <button type="submit" class="ghost-btn"> <?php echo $form_name === 'signup' ? "SIGN UP" : "SIGN IN";?></button>
          </div>
          <!-- link-area -->
          <div class="link-area">
              <?php if($form_name === 'signup'): ?>
                <a href="<?php echo route("signin") ?>" class="text-center">Click here to sign in</a>
              <?php elseif($form_name === 'signin'):?>
                <a href="<?php echo route("signup") ?>" class="text-center">Click here to register as a new user.</a>              
              <?php endif; ?>        
          <div>
        </form>
    </section>
</div>
<?php include('templates/layouts/footer.php') ?>
