<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach($projects as $project): ?>
            <li class="main-navigation__list-item <?php if (($selected_project_id) === $project['id']):?>main-navigation__list-item--active<?php endif;?>">
                <a class="main-navigation__list-item-link" href="/index.php?project_id=<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></a>
                <span class="main-navigation__list-item-count"><?= count_tasks($all_tasks, $project['title']); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="pages/form-project.html" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="post" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?= htmlspecialchars(get_post_value('search')) ?>" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
            <a href="/" class="tasks-switch__item">Повестка дня</a>
            <a href="/" class="tasks-switch__item">Завтра</a>
            <a href="/" class="tasks-switch__item">Просроченные</a>
        </nav>

        <label class="checkbox">
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?php if ($show_complete_tasks):?>checked<?php endif;?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <?php if (htmlspecialchars(get_post_value('search')) && !count($visible_tasks)): ?>
        <p>Ничего не найдено по вашему запросу</p>
    <?php endif;?>

    <table class="tasks">
        <?php foreach($visible_tasks as $key => $value): ?>
        <?php if ($value['status'] && !$show_complete_tasks): continue; endif; ?>
        <tr class="tasks__item task
        <?php if (check_important($value['deadline'])):?> task--important<?php endif;?>
        <?php if ($value['status']):?> task--completed<?php endif;?>">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden" type="checkbox" <?php if ($value['status']):?>checked<?php endif;?>>
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
