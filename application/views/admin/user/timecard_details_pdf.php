<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="width: 100%;">
<br/>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 50px; border: 0px;">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
            </td>

            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<?php
$designation = $this->db->where('designations_id', $profile_info->designations_id)->get('tbl_designations')->row();
$department = $this->db->where('departments_id', $designation->departments_id)->get('tbl_departments')->row();
?>
<div style="padding: 5px 0; width: 100%;">
    <div>
        <table style="width: 100%; border-radius: 3px;">
            <tr>
                <td style="width: 150px;">
                    <table style="border: 1px solid grey;">
                        <tr>
                            <td style="background-color: lightgray; border-radius: 2px;">
                                <?php if ($profile_info->avatar): ?>
                                    <img src="<?php echo base_url() . $profile_info->avatar; ?>"
                                         style="width: 138px; height: 144px; border-radius: 3px;">
                                <?php else: ?>
                                    <img alt="Employee_Image">
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="width: 500px; margin-left: 10px; margin-bottom: 10px; font-size: 13px;">
                        <tr>
                            <td style="width: 30%;"><h2><?php echo "$profile_info->fullname"; ?></h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%;"><strong><?= lang('emp_id') ?> : </strong></td>
                            <td style="width: 70%"><?php echo "$profile_info->employment_id "; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;"><strong><?= lang('departments') ?> : </strong></td>
                            <td style="width: 70%"><?php echo "$department->deptname"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;"><strong><?= lang('designation') ?> :</strong></td>
                            <td style="width: 70%"><?php echo "$designation->designations"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;"><strong><?= lang('joining_date') ?>: </strong></td>
                            <td style="width: 70%"><?= strftime(config_item('date_format'), strtotime($profile_info->joining_date)) ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
<br/>
<div style="width: 100%;">

    <div>
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?= lang('works_hours_deatils') ?><?php echo date('F-Y', strtotime($date));; ?></strong>
        </div>
        <?php
        define("SECONDS_PER_HOUR", 60 * 60);
        foreach ($attendace_info as $week => $v_attndc_info):

            ?>
            <div class="box-header" style="border-bottom: 1px solid red">
                <p class="box-title" style="font-size: 15px;">
                    <strong><?= lang('week') ?> : <?php echo $week; ?> </strong>
                </p>
            </div>
            <table style="width: 100%; /*border: 1px solid blue;*/ padding: 10px 0;">
                <tr style=" background: #E3E3E3;">
                    <?php
                    if (!empty($v_attndc_info)): foreach ($v_attndc_info as $date => $attendace):
                        $total_hour = 0;
                        $total_minutes = 0;
                        ?>
                        <th><?= strftime(config_item('date_format'), strtotime($date)) ?></th>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tr>
                <tr>
                    <?php
                    if (!empty($v_attndc_info)):foreach ($v_attndc_info as $date => $v_attendace):

                        $total_hh = 0;
                        $total_mm = 0;

                        ?>
                        <?php
                        if (!empty($v_attendace)) {
                            foreach ($v_attendace as $v_attandc) {
                                if (!empty($v_attandc->clockout_time)) {

                                    // calculate the start timestamp
                                    $startdatetime = strtotime($v_attandc->date_in . " " . $v_attandc->clockin_time);
                                    // calculate the end timestamp
                                    $enddatetime = strtotime($v_attandc->date_out . " " . $v_attandc->clockout_time);
                                    // calulate the difference in seconds
                                    $difference = $enddatetime - $startdatetime;
                                    // hours is the whole number of the division between seconds and SECONDS_PER_HOUR
                                    $hoursDiff = $difference / SECONDS_PER_HOUR;
                                    $total_hh += round($hoursDiff);
                                    // and the minutes is the remainder
                                    $minutesDiffRemainder = $difference % SECONDS_PER_HOUR / 60;
                                    $total_mm += round($minutesDiffRemainder) % 60;
                                    // output the result
                                    //echo round($hoursDiff) . " : " . round($minutesDiffRemainder) . " m";
                                } elseif (!empty($v_attandc->date) && $v_attandc->date == $date && $v_attandc->attendance_status == 'H') {
                                    $holiday = 1;
                                } elseif ($v_attandc->attendance_status == '3') {
                                    $leave = 1;
                                } elseif ($v_attandc->attendance_status == '0') {
                                    $absent = 1;
                                }
                            }
                        }

                        ?>
                        <td>

                            <?php
                            if ($total_mm > 60) {
                                $final_mm = $total_mm - 60;
                                $final_hh = $total_hh + 1;
                            } else {
                                $final_mm = $total_mm;
                                $final_hh = $total_hh;
                            }
                            $total_hour += $final_hh;
                            $total_minutes += $final_mm;
                            if ($final_hh != 0 || $final_mm != 0) {
                                echo $final_hh . " : " . $final_mm . " m";
                            } elseif (!empty($holiday)) {
                                echo '<span style="font-size: 12px;" class="label label-info std_p">' . lang('holiday') . '</span>';
                            } elseif (!empty($leave)) {
                                echo '<span style="font-size: 12px;" class="label label-warning std_p">' . lang('on_leave') . '</span>';
                            } elseif (!empty ($absent)) {
                                echo '<span style="font-size: 12px;" class="label label-danger std_p">' . lang('absent') . '</span>';
                            } else {
                                echo $final_hh . " : " . $final_mm . " m";
                            }
                            ?>
                        </td>
                        <?php
                        $holiday = NULL;
                        $leave = NULL;
                        $absent = NULL;
                    endforeach;
                    endif;
                    ?>
                </tr>
                <table>
                    <tr>
                        <td colspan="2" class="text-right">
                            <strong
                                style="margin-right: 10px; "><?= lang('total_working_hour') ?>
                                : </strong>
                        </td>
                        <td>
                            <?php
                            if ($total_minutes > 60) {
                                $total_minutes = $total_minutes - 60;
                                $total_hour = $total_hour + 1;
                            } else {
                                $total_minutes = $total_minutes;
                                $total_hour = $total_hour;
                            }
                            echo $total_hour . " : " . $total_minutes . " m";
                            ?>
                        </td>
                    </tr>
                </table>
            </table>
        <?php endforeach; ?>
    </div>
</div><!-- ***************** Salary Details  Ends *********************-->

</body>
</html>