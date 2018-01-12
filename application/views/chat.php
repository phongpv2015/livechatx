
    <?php if ( isset($_SESSION['id'])) {
        echo "<a href='http://www.livechatx.com/user/logout'>Logout</a>";
    } ?>
    <div id="container">

        <h2>Online Users</h2>
        <table width="45%" cellspacing="1" cellpadding="2" class="tableContent" style="margin-left:0px !important; text-align:center;">
            <tbody>
                <tr style="background-color:#9EB0E9;  font-size:13px; font-weight:bold; color:#fff;">
                    <th>Status</th>
                    <th>User Name</th>
                </tr>
                                  
            <?php
            if( isset($listOfUsers) ) {
                $status = [
                    0 => 'Offline',
                    1 => 'Online'
                ];
                foreach($listOfUsers as $key => $value) {
                    $link = '<a href="#" style="text-decoration:none">';
                    if( $_SESSION['uid'] != $value['id'] ) {
                        $link = '<a href="javascript:void(0)" onClick="javascript:chatWith(\''.$value['username'].'\','.$value['id'].')";>';
                    }
                    echo '<tr><td>'.$status[$value['status']].'</td><td>'.$link.$value['username'].'</td></tr>';
                }

            }
            ?>          
                
            </tbody>
        </table>
        
    </div>