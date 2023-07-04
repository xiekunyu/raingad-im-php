<?php

namespace utils;


class Tree
{
    public function createTreeByList($data, $pid = 0, $lv = 1, $idField = 'id', $pidField = 'pid', $child = 'tcd')
    {
        $tree = [];
        foreach ($data as $k => $v) {
            if ($v[$pidField] == $pid) {
                $v['tlv']  = $lv;
                $v[$child] = [];

                $tcd = $this->createTreeByList($data, $v[$idField], $lv + 1, $idField, $pidField, $child);
                if (!empty($tcd)) {
                    $v[$child] = $tcd;
                }
                $tree[] = $v;
            }
        }
        return $tree;
    }


    public function treeToList($tree, $ignore_tlv = 0)
    {
        $list = [];
        foreach ($tree as $k => $v) {
            if ($ignore_tlv == $v['tlv']) {
                $list[] = $v;
                continue;
            }

            $tcd = empty($v['tcd']) ? [] : $this->treeToList($v['tcd'], $ignore_tlv);

            if (isset($v['tcd'])) {
                unset($v['tcd']);
            }

            $list[] = $v;
            foreach ($tcd as $child) {
                $list[] = $child;
            }
        }
        return $list;
    }


    public function createListByTree($tree, $name = 'name', $ignore_pid = -1, $prefix = '')
    {
        $list  = [];
        $count = count($tree);

        foreach ($tree as $k => $v) {
            //名称前缀
            if ($ignore_pid != $v['pid']) {
                if ($k < $count - 1) {
                    $v[$name] = $prefix . '　├　' . $v[$name];
                    $tcd      = empty($v['tcd']) ? [] : $this->createListByTree($v['tcd'], $name, $ignore_pid, $prefix . '　│　');
                } else {
                    $v[$name] = $prefix . '　┕　' . $v[$name];
                    $tcd      = empty($v['tcd']) ? [] : $this->createListByTree($v['tcd'], $name, $ignore_pid, $prefix . '　　　');
                }
            } else {
                $v[$name] = $prefix . $v[$name];
                $tcd      = empty($v['tcd']) ? [] : $this->createListByTree($v['tcd'], $name, $ignore_pid, $prefix);
            }

            if (isset($v['tcd'])) {
                unset($v['tcd']);
            }
            $v['tcd'] = count($tcd);
            $list[]   = $v;
            foreach ($tcd as $child) {
                $list[] = $child;
            }
        }
        return $list;
    }

    //将数组保存文件
    public function saveArrayToFile($file, $array, $topNote = '')
    {
        $str = $this->arrayToString($array);
        $this->folders(dirname($file));

        if ($topNote) {
            $topNote = "/*" . $topNote . "*/\n\n";
        }
        return file_put_contents($file, "<?php \n" . $topNote . "return " . $str . ";");
    }

    private function arrayToString($array, $ref = '')
    {
        if (empty($array)) {
            return '[]';
        }

        $nowrap = true;
        $count  = count($array);

        if ($count <= 8) {
            foreach ($array as $k => $v) {
                if (empty($v)) {
                    continue;
                }

                if (!is_scalar($v)) {
                    $nowrap = false;
                    break;
                }
                if (is_string($k) && mb_strlen($k, 'utf-8') > 20) {
                    $nowrap = false;
                    break;
                }
                if (is_string($v) && mb_strlen($v, 'utf-8') > 60) {
                    $nowrap = false;
                    break;
                }
            }
        } else {
            $nowrap = false;
        }

        $str    = $nowrap ? "[" : "[\n";
        $newref = $nowrap ? '' : $ref . '    ';

        $i = 0;
        foreach ($array as $k => $v) {
            //key
            if (is_string($k)) {
                $str .= "$newref'$k' => ";
            } else {
                $str .= "{$newref}{$k} => ";
            }
            //value
            if (is_array($v)) {
                $str .= self::arrayToString($v, $ref . '    ');
            } elseif (is_int($v) || is_float($v)) {
                $str .= $v;
            } elseif (is_string($v)) {
                $str .= "'$v'";
            } elseif (is_bool($v)) {
                $str .= ($v ? 'true' : 'false');
            } else {
                //对象略
                $str .= "''";
            }
            if ($i == $count - 1) {
                $str .= $nowrap ? "" : "\n";
            } else {
                $str .= $nowrap ? ", " : ",\n";
            }
            $i++;
        }

        $str .= $nowrap ? "]" : $ref . "]";
        return $str;
    }

    /**
     * 创建文件夹
     * @param $dir
     * @return bool
     */
    public function folders($dir)
    {
        return is_dir($dir) || ($this->folders(dirname($dir)) && mkdir($dir));
    }
}
