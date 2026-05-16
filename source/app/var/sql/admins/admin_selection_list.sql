/* admin_selection_list */
SELECT `id`, `username`, `password`, `display_name`, `active`, `created_date`, `updated_date`
  FROM `admins`
 WHERE (:active IS NULL OR `active` = :active)
 ORDER BY `id`;
