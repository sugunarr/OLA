                <form name="update_loan_comment" method="get" action="loanview.php">
                  <input type="hidden" name="id" value="LOANID">
                  <table class="type2">
                    <tr>
                      <td class="type3">Resource Number</td>
                      <td>RESID</td>
                    </tr>
                    <tr>
                      <td class="type3">Title</td>
                      <td>TITLE</td>
                    </tr>
                    <tr>
                      <td class="type3">Author</td>
                      <td>AUTHOR</td>
                    </tr>
                    <tr>
                      <td class="type3">Loan ID</td>
                      <td>LOANID</td>
                    </tr>
                    <tr>
                      <td class="type3">Loan Date</td>
                      <td>LOANDATE</td>
                    </tr>
                    <tr>
                      <td class="type3">Due Date</td>
                      <td>DUEDATE</td>
                    </tr>
                    <tr>
                      <td class="type3">Person's Name</td>
                      <td>PERSONNAME</td>
                    </tr>
                    <tr>
                      <td class="type3">Person's Contact Information</td>
                      <td>PERSONCONTACT</td>
                    </tr>
                    <tr>
                      <td class="type3">Comments</td>
                      <td>
                        <input type="text" name="comments" value="COMMENTS" size=50 maxlength=50>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <input type="submit" name="submit" value="Update Comment">
                </form>

                <p>Mark Resource <a href="checkin.php?id=LOANID&status=returned">as Returned</a><br>
                Mark Resource <a href="checkin.php?id=LOANID&status=lost">as Lost</a></p>

                <p><a href="index.php">Click here</a> to leave this page 
                unchanged and return to the main volunteer menu.</p>

