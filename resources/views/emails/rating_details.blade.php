<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        

        /* Set the page content dimensions */
        body {
            width: 21cm;
            height: 29.7cm;
            margin: 0;
            
        }
        

        /* Style the inner div */
        .inner-div {
                background: white;
            
            padding: 0.5rem;
        }
        table {
           
            margin-bottom: 0px !important; 
        }

        table td {
            border: 1px solid #000; /* Add a 2px black border around each table cell */
            padding: 5px !important;
           
        }
        .yellow{
            color: #ffd700;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 page-content">
                <!-- Your content goes here -->
                
                <div class="inner-div">
                    <div class=""><h2 style="text-align: center;">Corporate Infotech Pvt. Ltd.</h2></div>
                    
                    <table class="table "  style="width: 100%;">
                        <tbody>
                             <tr>
                                <td class="font-weight-bold"><b>Job Application Name</b></td>
                                <td colspan="5"><?php echo ucfirst($ratingdetail->jobApplication->full_name); ?></td>
                               
                            </tr>
                             <tr>
                                <td class="font-weight-bold"><b>Total Rating  Point </b></td>
                                <td colspan="5" ><?php echo $ratingdetail->tottal_rating; ?></td>
                               
                            </tr>


                            <tr>
                                <td class="font-weight-bold"><b>Overall Personality</b></td>
                                <td colspan="5"><?php echo $ratingdetail->overall_personality; ?></td>
                               
                            </tr>
                            <tr>
                                <td class="font-weight-bold"><b>Mobility </b></td>
                                <td><?php echo $ratingdetail->mobility; ?></td>
                                 
                               
                            </tr>
                            <tr>
                                <td class="font-weight-bold"><b>Self Concept</b> </td>
                                <td><?php echo $ratingdetail->self_concept; ?></td>
                               
                               
                            </tr>
                            <tr>
                                <td  class="font-weight-bold"><b>Openness To Feedback</b></td>
                                <td style="word-wrap: break-word;"><?php echo $ratingdetail->openness_to_feedback; ?></td>
                                 
                            </tr>
                            <tr>
                                <td  class="font-weight-bold"><b>Drive </b></td>
                                <td class="font-weight-bold" style="word-wrap: break-word;"><?php echo $ratingdetail->drive; ?></td>
                                
                            </tr>
                            <tr>
                                <td  class="font-weight-bold"><b>Leadership Potential </b></td>
                                <td class="font-weight-bold" style="word-wrap: break-word;"><?php echo $ratingdetail->leadership_potential; ?></td>
                                
                            </tr>
                            <tr>
                                <td class="font-weight-bold col-6"><b>Personal Efficacy</b></td>
                                <td colspan="5"><?php echo $ratingdetail->personal_efficacy; ?></td>
                               
                            </tr>
                            <tr>
                                <td class="font-weight-bold"><b>Maturity Understanding</b></td>
                                <td colspan="5"><?php echo $ratingdetail->maturity_understanding; ?></td>
                               
                            </tr>
                            <tr>
                                <td class="font-weight-bold"><b>Comprehensibility Eloquence</b> </td>
                                <td colspan="5"><?php echo $ratingdetail->comprehensibility_eloquence; ?></td>
                               
                            </tr>
                            <tr>
                                <td class="font-weight-bold"><b>Knowledge Of Subject Job Product</b></td>
                                <td colspan="5"><?php echo $ratingdetail->knowledge_of_subject_job_product; ?></td>
                               
                            </tr>
                            <tr>
                                <td class="font-weight-bold"><b>Poise Mannerism</b></td>
                                <td colspan="5"><?php echo $ratingdetail->poise_mannerism; ?></td>
                               
                            </tr>
                            <tr>
                                <td colspan="2"><h3 style="text-align: center;">© 2023 Corporate Infotech Pvt. Ltd.. All rights reserved.</h3></td>
                            </tr>
                        </tbody>
                    </table>                   
                   
                </div>
            </div>
        </div>
    </div>  
</body>
</html>
