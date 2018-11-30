                <form name="update" method="get" action="update2.php">
                <table class="type2">
                  <tr>
                    <td class="type3">Resource Number</td>
                    <td>RESID<input type="hidden" name="id" value="RESID"></td>
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
                    <td>STATUSLIST (note: does not affect books currently on loan)</td>
                  </tr>
                  <tr>
                    <td class="type3">Subject</td>
                    <td>SUBJECTLIST</td>
                  </tr>
                  <tr>
                    <td class="type3">Title</td>
                    <td><input type="text" name="title" value="TITLE" size=50 maxlength=100></td>
                  </tr>
                  <tr>
                    <td class="type3">Author</td>
                    <td><input type="text" name="author" value="AUTHOR" size=50 maxlength=50></td>
                  </tr>
                  <tr>
                    <td class="type3">Year</td>
                    <td><input type="text" name="year" value="YEAR" size=4 maxlength=4></td>
                  </tr>
                  <tr>
                    <td class="type3">Isbn</td>
                    <td><input type="text" name="isbn" value="ISBN" size=20 maxlength=20></td>
                  </tr>
                  <tr>
                    <td class="type3">Comments</td>
                    <td><input type="text" name="comments" value="COMMENTS" size=50 maxlength=50></td>
                  </tr>
                  <tr>
                    <td class="type3">Date Aquired</td>
                    <td><input type="text" name="date_acquired" value="ACQUIRED" size=12 maxlength=12></td>
                  </tr>
                  <tr>
                    <td class="type3">Donated By</td>
                    <td><input type="text" name="donated_by" value="DONATED" size=20 maxlength=20></td>
                  </tr>
                </table>
                <br>
                <input type="submit" name="submit" value="Update">
                </form>

                <p><a href="index.php">Click here</a> to abandon this form
                and return to the main volunteer menu.</p>
