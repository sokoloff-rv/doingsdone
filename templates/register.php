<section class="content__side"></section>

<main class="content__main">
    <h2 class="content__main-heading">Регистрация аккаунта</h2>

    <form class="form" action="registration.php" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>
            <input class="form__input <?= isset($errors['email']) ? "form__input--error" : ""?> type="text" name="email" id="email" value="<?= htmlspecialchars(get_post_value('email')) ?>" placeholder="Введите e-mail">
            <?php if (isset($errors['email'])): ?>
                <p class="form__message"><?= $errors['email'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>

            <input class="form__input <?= isset($errors['password']) ? "form__input--error" : ""?>" type="password" name="password" id="password" value="<?= htmlspecialchars(get_post_value('password')) ?>" placeholder="Введите пароль">
            <?php if (isset($errors['password'])): ?>
                <p class="form__message"><?= $errors['password'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="name">Имя <sup>*</sup></label>

            <input class="form__input <?= isset($errors['name']) ? "form__input--error" : ""?>" type="text" name="name" id="name" value="<?= htmlspecialchars(get_post_value('name')) ?>" placeholder="Введите имя">
            <?php if (isset($errors['name'])): ?>
                <p class="form__message"><?= $errors['name'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form__row form__row--controls">
            <?php if (isset($errors['email']) || isset($errors['password']) || isset($errors['name'])): ?>
                <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>

            <input class="button" type="submit" name="" value="Зарегистрироваться">
        </div>
    </form>
</main>
