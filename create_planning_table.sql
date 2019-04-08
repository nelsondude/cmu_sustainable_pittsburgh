DROP TABLE IF EXISTS `zpaq2_planned_actions`;
CREATE TABLE `zpaq2_planned_actions`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `action_id`  int(11) NOT NULL,
    `user_id`    int(11) NOT NULL,
    `company_id` int(11) NOT NULL,
    `deadline` DATE,
    PRIMARY KEY (`id`),
    FOREIGN KEY (action_id) REFERENCES zpaq2_gwc_actions(id),
    FOREIGN KEY (user_id) REFERENCES zpaq2_users(id),
    FOREIGN KEY (company_id) REFERENCES zpaq2_gwc_companies(id)
);
