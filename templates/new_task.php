<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project): ?>
            <li class="main-navigation__list-item <?php if (($selected_project_id) === $project['id']):?>main-navigation__list-item--active<?php endif;?>">
                <a class="main-navigation__list-item-link" href="/index.php?project_id=<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></a>
                <span class="main-navigation__list-item-count"><?= count_tasks($connect, $project['title'], $user_id); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление задачи</h2>

    <form class="form" action="add.php" method="post" autocomplete="off" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?= isset($errors['name']) ? "form__input--error" : ""?>" type="text" name="name" id="name" value="<?= htmlspecialchars(get_post_value('name')) ?>" placeholder="Введите название">
            <?php if (isset($errors['name'])): ?>
                <p class="form__message"><?= $errors['name'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?= isset($errors['project']) ? "form__input--error" : ""?>" name="project" id="project">
                <?php foreach ($projects as $project): ?>
                <li class="main-navigation__list-item <?php if (($selected_project_id) === $project['id']):?>main-navigation__list-item--active<?php endif;?>">
                    <option <?= htmlspecialchars(get_post_value('project')) === $project['id'] ? "selected" : "" ?> value="<?= $project['id'] ?>"><?= $project['title'] ?></option>
                </li>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['project'])): ?>
                <p class="form__message"><?= $errors['project'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date <?= isset($errors['date']) ? "form__input--error" : ""?>" type="text" name="date" id="date" value="<?= htmlspecialchars(get_post_value('date')) ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <?php if (isset($errors['date'])): ?>
                <p class="form__message"><?= $errors['date'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="file">Файл</label>

            <div class="form__input-file <?= isset($errors['file']) ? "form__input--error" : ""?>">
                <input class="visually-hidden" type="file" name="file" id="file" value="<?= htmlspecialchars(get_post_value('file')) ?>">

                <label class="button button--transparent" for="file">
                    <span>Выберите файл</span>
                </label>
            </div>
            <?php if (isset($errors['file'])): ?>
                <p class="form__message"><?= $errors['file'] ?></p>
            <?php endif; ?>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
