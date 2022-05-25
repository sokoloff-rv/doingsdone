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

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?= htmlspecialchars(filter_input(INPUT_GET, 'search')) ?>" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item <?= !$_GET['deadline'] ? "tasks-switch__item--active" : "" ?>">Все задачи</a>
            <a href="/index.php?deadline=today" class="tasks-switch__item <?= $_GET['deadline'] === "today" ? "tasks-switch__item--active" : "" ?>">Повестка дня</a>
            <a href="/index.php?deadline=tomorrow" class="tasks-switch__item <?= $_GET['deadline'] === "tomorrow" ? "tasks-switch__item--active" : "" ?>">Завтра</a>
            <a href="/index.php?deadline=overdue" class="tasks-switch__item <?= $_GET['deadline'] === "overdue" ? "tasks-switch__item--active" : "" ?>">Просроченные</a>
        </nav>

        <label class="checkbox">
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?php if ($show_complete_tasks):?>checked<?php endif;?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <?php if (htmlspecialchars(filter_input(INPUT_GET, 'search')) && !count($visible_tasks)): ?>
        <p>Ничего не найдено по вашему запросу</p>
    <?php endif;?>

    <table class="tasks">
        <?php foreach ($visible_tasks as $key => $value): ?>
        <?php if ($value['status'] && !$show_complete_tasks): continue; endif; ?>
        <?php $class = "";
        if (check_important($value['deadline'])) {
            $class = " task--important";
        }
        if ($value['status']) {
            $class = $class." task--completed";
        } ?>
        <tr class="tasks__item task<?= $class ?>">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?php if ($value['status']):?>checked<?php endif;?> value="<?= $value['id'] ?>">
                    <span class="checkbox__text"><?= htmlspecialchars($value['title']) ?></span>
                </label>
            </td>
            <td class="task__file">
                <?php if ($value['filepath']): ?>
                    <a class="download-link" href="<?= $value['filepath'] ?>">Скачать файл</a>
                <?php endif;?>
            </td>            
            <td class="task__date"><?= $value['deadline'] ? date("Y-m-d", strtotime(htmlspecialchars($value['deadline']))) : 'Без даты' ?></td>
            <td class="task__controls"></td>
        </tr>
        <?php endforeach; ?>

    </table>
</main>
