<?php
$current_category = $_GET['category'] ?? '';
$group_home       = getGroupList();
$imageUrl         = _IMAGE_PATH;
?>
<section class="category-section">
    <div class="container category-container">
        <div class="category-grid">
            <?php
            if ($group_home && $group_home->num_rows > 0) {
                while ($group = $group_home->fetch_assoc()) {
                    $picture   = htmlspecialchars($group['picture']);
                    $groupName = htmlspecialchars($group['group_name']);
                    $isActive  = (!empty($current_category) && strcasecmp($current_category, $group['group_name']) === 0);
            ?>
                <a href="<?= _BASEURL ?>product-list.php?category=<?= urlencode($group['group_name']) ?>" 
                   class="category-card <?= $isActive ? 'active' : '' ?>">
                    <div class="category-img-wrap">
                        <img src="<?= $imageUrl ?>group-master/<?= $picture ?>" alt="<?= $groupName ?>" loading="lazy">
                    </div>
                    <div class="category-info">
                        <span class="category-title"><?= $groupName ?></span>
                        <span class="category-action">Shop Now <i class="icon-arrow-right"></i></span>
                    </div>
                </a>
            <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<style>
    /* ================= CATEGORY SECTION BASE ================= */
    .category-section {
        padding: 24px 0 28px;
        background: #f8f9fb;
        border-bottom: 1px solid #ebecef;
    }

    .category-container {
        margin-bottom: 0px;
        margin-top: 0px;
    }

    /* ================= DESKTOP GRID & CARDS ================= */
    .category-grid {
        display: flex;
        justify-content: center;
        align-items: stretch;
        gap: 24px;
        flex-wrap: wrap;
        max-width: 1100px;
        margin: 0 auto;
    }

    .category-card {
        flex: 1 1 240px;
        max-width: 320px;
        min-width: 220px;
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        text-decoration: none !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        border: 1px solid #eef0f3;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .category-img-wrap {
        width: 100%;
        height: 180px;
        overflow: hidden;
        background: #f1f3f7;
        position: relative;
    }

    .category-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
        display: block;
    }

    .category-info {
        padding: 14px 18px;
        background: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f3f6;
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    .category-title {
        font-size: 14.5px;
        font-weight: 700;
        color: #1f2937;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        margin: 0;
        transition: color 0.3s ease;
    }

    .category-action {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    /* Desktop Hover Effects */
    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
        border-color: #cbd5e1;
    }

    .category-card:hover .category-img-wrap img {
        transform: scale(1.08);
    }

    .category-card:hover .category-title {
        color: #0f172a;
    }

    .category-card:hover .category-action {
        color: #37475a;
        transform: translateX(4px);
    }

    /* Active State (Desktop) */
    .category-card.active {
        border: 2px solid #37475a;
        box-shadow: 0 8px 24px rgba(55, 71, 90, 0.16);
    }

    .category-card.active .category-info {
        background: #37475a;
        border-top-color: #37475a;
    }

    .category-card.active .category-title {
        color: #ffffff;
    }

    .category-card.active .category-action {
        color: #f1f5f9;
    }

    /* ================= TABLET ================= */
    @media (max-width: 992px) {
        .category-grid {
            gap: 16px;
        }

        .category-card {
            min-width: 180px;
        }

        .category-img-wrap {
            height: 150px;
        }

        .category-title {
            font-size: 13.5px;
        }
    }

    /* ================= MOBILE (HORIZONTAL STORY/ICON REEL) ================= */
    @media (max-width: 768px) {
        .category-section {
            padding: 10px 0 12px;
            background: #ffffff;
            border-bottom: 1px solid #f0f0f2;
        }

        .category-container {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
        }

        .category-grid {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            gap: 16px;
            padding: 4px 16px 4px;
            scrollbar-width: none;
            -ms-overflow-style: none;
            flex-wrap: nowrap;
        }

        .category-grid::-webkit-scrollbar {
            display: none;
        }

        .category-card {
            flex: 0 0 auto;
            width: 76px;
            min-width: 76px;
            max-width: 76px;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0;
            transform: none !important;
        }

        .category-img-wrap {
            width: 66px;
            height: 66px;
            border-radius: 50% !important;
            overflow: hidden;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
            margin: 0 auto;
        }

        .category-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .category-info {
            padding: 6px 0 0;
            background: transparent !important;
            border-top: none !important;
            display: block;
            text-align: center;
            width: 100%;
        }

        .category-title {
            font-size: 11.5px;
            font-weight: 600;
            color: #334155;
            line-height: 1.25;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: capitalize;
            word-break: break-word;
            letter-spacing: 0;
            margin: 0;
        }

        .category-action {
            display: none !important;
        }

        /* Mobile Active State */
        .category-card.active .category-img-wrap {
            border-color: #37475a;
            box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #37475a;
            transform: scale(1.04);
        }

        .category-card.active .category-title {
            color: #0f172a;
            font-weight: 700;
        }
    }

    @media (max-width: 480px) {
        .category-grid {
            gap: 14px;
            padding: 4px 12px 4px;
        }

        .category-card {
            width: 72px;
            min-width: 72px;
            max-width: 72px;
        }

        .category-img-wrap {
            width: 62px;
            height: 62px;
        }

        .category-title {
            font-size: 11px;
        }
    }
</style>