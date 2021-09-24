<?php

class AdminerShowTableRow {
    function head() {
        if (isset($_GET["select"])) {
            ?>
<script <?php echo nonce(); ?>>
document.addEventListener('DOMContentLoaded', function() {
  var table = document.querySelector('table#table');
  table.querySelectorAll('tbody tr td:first-child').forEach(function(td) {
    var a = td.querySelector('a.edit');
    if (a) {
      td.innerHTML += ' <a href="' + a.href + '&show=1"><?php echo lang('Show'); ?></a>';
    }
  });
});
</script>
<?php
        }
        if (isset($_GET["show"])) {
            ?>
<script <?php echo nonce(); ?>>
document.addEventListener('DOMContentLoaded', function() {
  var p = document.querySelector('p#breadcrumb');
  if (p) {
            p.innerHTML = p.innerHTML.replace(/<?php echo lang('Edit'); ?>\s*$/, '<?php echo lang('Show'); ?>');
  }
  var h2 = document.querySelector('div#content h2');
  if (h2) {
    h2.innerHTML = h2.innerHTML.replace('<?php echo lang('Edit'); ?>', '<?php echo lang('Show'); ?>');
  }
});
</script>
<?php
        }
        if (isset($_GET["edit"])) {
?>
<script <?php echo nonce(); ?>>
document.addEventListener('DOMContentLoaded', function() {
  var params = [];
  document.querySelectorAll('div#content table tr th:nth-child(1)').forEach(function(th) {
      params.push(th.innerText);
  });
  var tr1 = document.querySelector('div#content table tr:nth-child(1)');
  for(let [param, value] of new URLSearchParams(location.search)){
    if (param.match(/^where/)) {
      param = param.replace('where[', '').replace(']', '');
      console.log(params.includes(param));
      if (!params.includes(param)) {
        var tr = document.createElement('tr');
<?php
        if (isset($_GET["show"])) {
?>
            tr.innerHTML = "<th>" + param + "</th><td>" + value + "</td>";
<?php
        } else {
?>
            tr.innerHTML = "<th>" + param + "</th><td></td><td>" + value + "</td>";
<?php
        }
?>
        tr1.parentNode.insertBefore(tr, tr1);
      }
    }
  }
});
</script>
<?php
        }
    }

    private function driver() {
        static $driver = false;
        if ($driver === false) {
            foreach ($GLOBALS as $k => $v) {
                if (is_object($v) && get_class($v) == 'Min_Driver') {
                    $driver = $v;
                    break;
                }
            }
            if ($driver === false) {
                $driver = null;
            }
        }
        return $driver;
    }

    function editRowPrint($table, $fields, $row, $update) {
        if (!isset($_GET["show"])) return;

        $referer = $_SERVER["HTTP_REFERER"];
        if ($referer) {
            $back = lang('Back');
            echo '<a href="' . $referer . '">' . $back . '</a>';
        }
        echo "<table cellspacing='0' class='layout'>";
        $driver = $this->driver();
        foreach ($row as $key => $val) {
            $field = $fields[$key];
            if ($driver) {
                $val = $driver->value($val, $field);
            }
            $val = select_value($val, null, $field, null);
            echo "<tr><th>$key</th><td>$val</td></tr>";
        }
        echo "</table>";

        $checks = array();
        foreach (explode('&', $_SERVER['QUERY_STRING']) as $query) {
            if (preg_match('/^where%5B.+%5D=/', $query)) {
                $checks[] = $query;
            }
        }
        $check = implode('&', $checks);
        $token = get_token();
        $clone = lang('Clone');
        echo "<p>";
        echo "<form action=\"$referer\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"token\" value=\"$token\">";
        echo "<input type=\"hidden\" name=\"check[]\" value=\"$check\">";
        echo "<input type=\"submit\" name=\"clone\" value=\"$clone\">";
        echo "</form>";
        echo "</p>";

        page_footer();
        exit;
    }
}
