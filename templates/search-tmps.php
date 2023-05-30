<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= $search; ?></span>»</h2>
        <?php if (!empty($lots)): ?>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= $lot['img']; ?>" width="350" height="260" alt="Сноуборд">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= $lot['name_category']; ?></span>
                    <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?= $lot['id']; ?>"><?= $lot['title']; ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?= number_format($lot['start_price']); ?></span>
                        </div>
                        <?php $timer = get_dt_range($lot['date_finish']); ?>
                        <div class="lot__timer timer <?php if ($timer[0] < 1) : echo "timer--finishing"; endif; ?>">
                            <?= "$timer[0] : $timer[1]";?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php if ($total_page > 1): ?>
    <ul class="pagination-list">
        <?php $prev = $page_number - 1; ?>
        <?php $next = $page_number + 1; ?>
        <li class="pagination-item pagination-item-prev">
            <a <?php if ($page_number >= 2): ?> href="/search.php?search=<?= $search; ?>&page=<?= $prev; ?>">Назад</a><?php endif; ?>
        </li>
        <?php foreach ($pages as $page): ?>
        <li class="pagination-item <?php if ($page === $page_number): ?>pagination-item-active<?php endif; ?>">
            <a href="/search.php?search=<?= $search;?>&page=<?= $page; ?>"><?= $page?></a>
        </li>
        <?php endforeach; ?>
        <li class="pagination-item pagination-item-next">
            <a <?php if ($page_number < $total_page): ?> href="/search.php?search=<?= $search;?>&page=<?= $next; ?>">Вперед</a><?php endif; ?>
        </li>
    </ul>
    <?php endif; ?>
</div>
<div>
<?php else: ?>
    <h2>Нечего не найдено по вашему запросу</h2>
<?php endif; ?>
</div>

