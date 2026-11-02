-- Indexes for per-client event counts/debts (event dialog, tablerooms) and the log page date filter.
-- cid is a prefix of cid_state_date, so the old single-column index is dropped.
ALTER TABLE `events` DROP INDEX `cid`, ADD INDEX `cid_state_date` (`cid`, `state`, `date`);
ALTER TABLE `log` ADD INDEX `created` (`created`);

-- Production stats were empty after the server migration (Cardinality 0), which makes MySQL pick the `state` index
ANALYZE TABLE `events`, `log`, `clients`, `client_contacts`, `event_weekly`, `event_templates`;
