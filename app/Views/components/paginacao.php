<?php

/** @var \CodeIgniter\Pager\PagerRenderer $pager */
$pager->setSurroundCount(2);
?>

<div class="w3-center w3-margin-top w3-padding">
    <div class="w3-bar w3-border w3-round w3-white w3-card-custom">

        <?php if ($pager->hasPrevious()) : ?>
            <a href="<?= $pager->getFirst() ?>" class="w3-button w3-hover-light-grey" title="Primeira">&laquo;&laquo;</a>
            <a href="<?= $pager->getPrevious() ?>" class="w3-button w3-hover-light-grey" title="Anterior">&laquo;</a>
        <?php else: ?>
            <button class="w3-button w3-disabled" disabled>&laquo;&laquo;</button>
            <button class="w3-button w3-disabled" disabled>&laquo;</button>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <a href="<?= $link['uri'] ?>" class="w3-button <?= $link['active'] ? 'w3-ocre w3-bold' : 'w3-hover-light-grey' ?>">
                <?= $link['title'] ?>
            </a>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <a href="<?= $pager->getNext() ?>" class="w3-button w3-hover-light-grey" title="Próxima">&raquo;</a>
            <a href="<?= $pager->getLast() ?>" class="w3-button w3-hover-light-grey" title="Última">&raquo;&raquo;</a>
        <?php else: ?>
            <button class="w3-button w3-disabled" disabled>&raquo;</button>
            <button class="w3-button w3-disabled" disabled>&raquo;&raquo;</button>
        <?php endif ?>

    </div>
</div>