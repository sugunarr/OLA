                <form name="add" method="get" action="add2.php">
                <table class="type2">
                  <tr>
                    <td class="type3">Resource Number</td>
                    <td>[System Assigned]</td>
                  </tr>
                  <tr>
                    <td class="type3">Location</td>
                    <td>LOCATIONLIST</td>
                  </tr>
                  <tr>
                    <td class="type3">Media Type</td>
                    <td>MEDIALIST</td>
                  </tr>
                  <tr>
                    <td class="type3">Status</td>
                    <td>STATUSLIST (* Required)</td>
                  </tr>
                  <tr>
                    <td class="type3">Subject</td>
                    <td>SUBJECTLIST (* Required)</td>
                  </tr>
                  <tr>
                    <td class="type3">Title</td>
                    <td><input type="text" name="title" size=50 maxlength=100> (* Required)</td>
                  </tr>
                  <tr>
                    <td class="type3">Author</td>
                    <td><input type="text" name="author" size=50 maxlength=50></td>
                  </tr>
                  <tr>
                    <td class="type3">Year</td>
                    <td><input type="text" name="year" size=4 maxlength=4></td>
                  </tr>
                  <tr>
                    <td class="type3">ISBN</td>
                    <td><input type="text" name="isbn" size=20 maxlength=20></td>
                  </tr>
                  <tr>
                    <td class="type3">Publisher</td>
                    <td><input type="text" name="comments" size=50 maxlength=50></td>
                  </tr>
                  <tr>
                    <td class="type3">Date Aquired</td>
                    <td><input type="text" name="date_acquired" size=12 maxlength=12></td>
                  </tr>
                  <tr>
                    <td class="type3">Donated By</td>
                    <td><input type="text" name="donated_by" size=20 maxlength=20></td>
                  </tr>
                 <tr>
                    <td class="type3">Currency</td>
                    <td><input type="text" name="currency" size=20 maxlength=20></td>
                  </tr>				  
                  <tr>
                    <td class="type3">Price</td>
                    <td><input type="text" name="final_price" size=20 maxlength=20></td>
                  </tr>
                   <tr>
                    <td class="type3">Keywords</td>
                    <td><textarea name="keywords" cols=40 rows=5></textarea></td>
                  </tr>
				  
                </table>
                <br>
                <input type="submit" name="submit" value="Add">
                </form>

                <p><a href="index.php">Click here</a> to abandon this form
                and return to the main volunteer menu.</p>
