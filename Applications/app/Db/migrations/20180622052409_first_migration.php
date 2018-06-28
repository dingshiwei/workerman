<?php


use Phinx\Migration\AbstractMigration;

class FirstMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->execute("CREATE TABLE `user_clients` (`id` int(11) NOT NULL COMMENT '主键ID' AUTO_INCREMENT,
            `client_id` char(32) NOT NULL COMMENT 'ws客户端id',
            `vuid` int(11) NOT NULL COMMENT '用户vuid',
            `ip` char(15) NOT NULL COMMENT 'ip地址',
            `update_at` datetime NOT NULL COMMENT '最后更新时间',
            `create_at` datetime NOT NULL COMMENT '创建时间',
             PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户端id存储表'");
    }
}
