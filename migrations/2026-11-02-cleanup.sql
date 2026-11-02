-- Dead tables: sync to the old boomroom.by site (off since 2020-10-30), unused sessions table, 2017 backup copy of templates
DROP TABLE IF EXISTS `sync_tasks`, `sync`, `sessions`, `event_templates_pre_22_08_2017`;

-- Log retention: 3 years (afterwards the monthly cron api.php?task=cleanLog keeps it so)
DELETE FROM `log` WHERE `created` < NOW() - INTERVAL 3 YEAR;
OPTIMIZE TABLE `log`;
