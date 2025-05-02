<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract - {{ $contract->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 20px;
            padding-left: 50px;
            padding-right: 50px;
            font-size: 13.5px;
            line-height: 1.5;
            text-align: justify;
        }
        .page-break {
            page-break-after: always;
        }
        div {
            font-size: 13.5px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div>
        <h1 style="text-align: center; font-size: 26px; font-weight: bold;">CURRICULUM PROGRAMME RIGHT-TO-USE AGREEMENT</h1>
    </div>

    <div class="page-break">
        <div style="text-align: center;">
            <img src="{{ public_path('images/anf-logo.jpg') }}" style="width: 150px; height: auto; margin-bottom: 30px;" alt="ANF Logo">
        </div>
        <div style="text-align: center; font-size: 20px; margin-bottom: 70px;">
            BETWEEN
        </div>
        <div style="text-align: center; font-size: 20px; margin-bottom: 5px; font-weight: bold;">
            ALFA AND FRIENDS SDN.BHD.
        </div>
        <div style="text-align: center; font-size: 20px; margin-bottom: 70px;">
            (SSM NO.: No. 201601005690)
        </div>
        <div style="text-align: center; font-size: 20px; margin-bottom: 70px;">
            AND
        </div>
        <div style="text-align: center; font-size: 20px; margin-bottom: 5px; font-weight: bold;">
            {{ $contract->name }}
        </div>
        <div style="text-align: center; font-size: 20px; margin-bottom: 150px;">
            ({{ $contract->ssm_ic }})
        </div>
        <div style="text-align: center; font-size: 20px; margin-bottom: 30px;">
            DATED: {{ \Carbon\Carbon::parse($contract->date)->format('d / m / Y') }}
        </div>
    </div>
    <div class="page-break">
        <div style="text-align: center; margin-bottom: 15px;">
            <span style="font-weight: bold;">THIS CURRICULUM PROGRAMME(S) RIGHT-TO-USE AGREEMENT</span><br><br>
            is made on the <span style="font-weight: bold;">{{ \Carbon\Carbon::parse($contract->date)->format('jS \d\a\y \o\f F Y') }}</span>,
        </div>
        <div style="margin-bottom: 15px;">
            Between<br><br>
            <span style="font-weight: bold;">ALFA AND FRIENDS SDN. BHD. (SSM NO.: No. 201601005690)</span> a Malaysian registered company and having an address at No 36 JALAN BP 7/8, BANDAR BUKIT PUCHONG, 47120 PUCHONG, SELANGOR.<br><br>
            (hereinafter called "<span style="font-weight: bold;">the Company</span>") of the one part<br><br>
            and<br><br>
            <span style="font-weight: bold;">{{ $contract->name }}</span> ({{ $contract->id_type == 'SSM' ? 'SSM No.' : 'NRIC No.' }}: {{ $contract->ssm_ic }}) a {{ $contract->id_type == 'SSM' ? 'Malaysian registered company' : 'Malaysian' }} and having an address at {{ $contract->address }}.<br><br>
            (hereinafter called "<span style="font-weight: bold;">the Customer</span>") of the second part.<br><br>
            Whereas it is mutually agreed as follow:
        </div>
        <div style="margin-bottom: 30px;">
            <table style="width: 100%; text-align: justify; page-break-inside: auto;">
                <tr>
                    <td style="vertical-align: top; padding-right: 10px;">1. </td>
                    <td>The Company shall provide the programme(s), which shall constitute part of the yearly curriculum programme(s) for the Kindergarten, its branches and its franchisees of Toddler (playgroup, age 3), Nursery, K1 & K2 students.</td>
                </tr>   
                <tr>
                    <td style="vertical-align: top; padding-right: 10px;">2. </td>
                    <td>The Kindergarten, its branches and its franchisees shall use and ensure that ALL its students involved in the programme(s) package acquire all materials necessary for the Company's activities and lessons included in the package.</td>
                </tr>   
                <tr>
                    <td style="vertical-align: top; padding-right: 10px;">3. </td>
                    <td>The agreed upon fees are as below:</td>
                </tr>   
            </table>
            <table style="margin-left: 25px; margin-bottom: 10px; text-align: justify;">
                <tr>
                    <td style="padding-top: 10px; font-weight: bold;">a. </td>
                    <td style="padding-top: 10px; font-weight: bold;">Right-To-Use Fee</td>
                </tr>
                <tr>
                    <td></td>
                    <td>The agreed pricing with the conditions applied to the right-to-use fee are outlined as follows:</td>
                </tr>
                <tr>
                    <td style="margin-top: 20px;"></td>
                    <td style="margin-top: 20px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <tr>
                                <td style="border: 1px solid black; font-weight: bold; padding-left: 10px; padding-right: 10px;" width="50%">OFFERING</td>
                                <td style="text-align: center; border: 1px solid black; font-weight: bold;" width="25%">PRICE</td>
                                <td style="text-align: center; border: 1px solid black; font-weight: bold;" width="25%">PROMO PRICE</td>
                            </tr>
                            @foreach($contract->right_to_use_fees as $fee)
                                <tr>
                                    <td style="border: 1px solid black; padding-left: 10px; padding-right: 10px;">
                                        {{ $fee->program }}
                                    </td>
                                    <td style="border: 1px solid black; text-align: center;">RM {{ $fee->price, 2 }}</td>
                                    <td style="border: 1px solid black; text-align: center;">RM {{ $fee->promo_price, 2 }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top; padding-top: 10px;">i. </td>
                    <td style="padding-top: 10px;">The right-to-use fee grants the customer permission to utilize the company programme(s).</td>
                </tr>
                <tr>
                    <td style="vertical-align: top; padding-bottom: 10px;">ii. </td>
                    <td>Should the customer decide not to place any orders for student kits across all programme(s) for a consecutive 12-months period, the right-to-use fee will be forfeited.                                </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">iii. </td>
                    <td>In order to initiate the ordering process for student kits across any programme(s), the customer is required to submit payment for a new right-to-use fee.                                </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">iv. </td>
                    <td>Delivery fees are excluded from the pricing. The customer is required to pay the delivery fee charges.                                </td>
                </tr>
                <tr>
                    <td style="padding-top: 10px; font-weight: bold;">b. </td>
                    <td style="padding-top: 10px; font-weight: bold;">Student Fee</td>
                </tr>
                <tr>
                    <td></td>
                    <td>The agreed pricing with the conditions applied to the student fee are outlined as follows:</td>
                </tr>
                <tr>
                    <td style="margin-top: 20px; padding-bottom: 10px;"></td>
                    <td style="margin-top: 20px; padding-bottom: 10px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <tr>
                                <td style="border: 1px solid black; font-weight: bold; padding-left: 10px; padding-right: 10px;" width="50%">OFFERING</td>
                                <td style="text-align: center; border: 1px solid black; font-weight: bold;" width="25%">PRICE PER YEAR</td>
                                <td style="text-align: center; border: 1px solid black; font-weight: bold;" width="25%">PROMO PRICE</td>
                            </tr>
                            @foreach($contract->student_fees as $fee)
                                <tr>
                                    <td style="border: 1px solid black; padding-left: 10px; padding-right: 10px;">
                                        {{ $fee->offering }}
                                    </td>
                                    <td style="border: 1px solid black; text-align: center;">RM {{ $fee->price_per_year, 2 }}</td>
                                    <td style="border: 1px solid black; text-align: center;">RM {{ $fee->promo_price, 2 }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">i. </td>
                    <td>The stated price encompasses a student kit and includes two (2) activity books per programme(s) for a duration of one (1) year.</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">ii. </td>
                    <td>A minimum order quantity of twenty (20) student kits per programme(s) is required, with at least five (5)  student kits per level per programme(s).</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">iii. </td>
                    <td>Delivery fees are excluded from the pricing. The customer is required to pay the delivery fee charges.</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">iv. </td>
                    <td>The student kits will be delivered once a year or twice a year as the programme listed below:</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;"></td>
                    <td style="vertical-align: top;">
                        <ul style="padding-left: 13.5px; padding-top: 5px; margin-top: 0;">
                            <li>Little Scientists Programme – Two (2) times per year</li>
                            <li>Little Bot Programme – One (1) time a year</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 10px; font-weight: bold;">c. </td>
                    <td style="padding-top: 10px; font-weight: bold;">Supplementary Service and Product Fee</td>
                </tr>
                <tr>
                    <td></td>
                    <td>The agreed pricing with the conditions applied to the supplementary service and Product fee are outlined as follows:</td>
                </tr>
                <tr>
                    <td style="margin-top: 20px; padding-bottom: 10px;"></td>
                    <td style="margin-top: 20px; padding-bottom: 10px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <tr>
                                <td style="border: 1px solid black; font-weight: bold; padding-left: 10px; padding-right: 10px;" width="50%">OFFERING</td>
                                <td style="text-align: center; border: 1px solid black; font-weight: bold;" width="25%">PRICE</td>
                                <td style="text-align: center; border: 1px solid black; font-weight: bold;" width="25%">PROMO PRICE</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding-left: 10px; padding-right: 10px;">
                                    Teaching Aid / Programme
                                </td>
                                <td style="border: 1px solid black; text-align: center;">RM 1000</td>
                                <td style="border: 1px solid black; text-align: center;">RM {{ $contract->teaching_aid_promo }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding-left: 10px; padding-right: 10px;">
                                    Lesson Plan / Book
                                </td>
                                <td style="border: 1px solid black; text-align: center;">RM 25</td>
                                <td style="border: 1px solid black; text-align: center;">RM {{ $contract->lesson_plan_promo }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding-left: 10px; padding-right: 10px;">
                                    Centralise Training / Teacher / Session
                                </td>
                                <td style="border: 1px solid black; text-align: center;">RM 50</td>
                                <td style="border: 1px solid black; text-align: center;">RM {{ $contract->centralise_training_promo }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding-left: 10px; padding-right: 10px;">
                                    In-House Training (Klang Valley) / Hour
                                </td>
                                <td style="border: 1px solid black; text-align: center;">RM 200</td>
                                <td style="border: 1px solid black; text-align: center;">RM {{ $contract->inhouse_training_kv_promo }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding-left: 10px; padding-right: 10px;">
                                    In-House Training (Peninsular Malaysia) / Hour
                                </td>
                                <td style="border: 1px solid black; text-align: center;">RM 300</td>
                                <td style="border: 1px solid black; text-align: center;">RM {{ $contract->inhouse_training_north_promo }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div>
                <table style="width: 100%; text-align: justify; page-break-inside: auto;">
                    <tr>
                        <td style="vertical-align: top; padding-right: 10px;">4. </td>
                        <td>The products comprised within the package shall be dispatched to the Customer solely upon receipt of full payment by the Company. Further particulars regarding product to be delivered are outlined in <span style="font-style: italic; font-weight: bold;">Appendix 1</span>.</td>
                    </tr>   
                    <tr>
                        <td style="vertical-align: top; padding-right: 10px;">5. </td>
                        <td>The Customer, its branches, its franchisees and all its teachers involved in the coordinating of the adopted programme(s) package shall not reproduce, store in a retrieval system or transmitted in any form or by any means, electronic, mechanical, photocopying, and recording, alter and tamper no part of the Company's materials without the prior consent of the copyright owner.</td>
                    </tr>   
                    <tr>
                        <td style="vertical-align: top; padding-right: 10px;">6. </td>
                        <td>The Customer, its branches and its franchisees must not disclose any trade secrets or other information of a confidential nature relating to the Company or their business or in respect of which the Company owes an obligation of confidence to any third party during or after the term of this Contract except in the proper course of this Contract or as required by law.The Customer, its branches and its franchisees must not disclose any trade secrets or other information of a confidential nature relating to the Company or their business or in respect of which the Company owes an obligation of confidence to any third party during or after the term of this Contract except in the proper course of this Contract or as required by law.</td>
                    </tr>   
                    <tr>
                        <td style="vertical-align: top; padding-right: 10px;">7. </td>
                        <td>For the entire duration of this Contract, the Customer, its branches and its franchisees shall not acquire the services of any other party, who in any form, provides programme(s) of competitive nature to that provided by the Company.</td>
                    </tr>   
                    <tr>
                        <td style="vertical-align: top; padding-right: 10px;">8. </td>
                        <td>The Company reserves the right to make improvements, alterations and changes in whatever forms it deems rightful to the programme(s) package to ensure the quality of the programme(s) package herein-mentioned.</td>
                    </tr>   
                    <tr>
                        <td style="vertical-align: top; padding-right: 10px;">9. </td>
                        <td>The Customer will not be affected if there are any price changes for the first three (3) years. After the first three (3) years, the Company has the right to make changes to the price, the Customer will be charged according to the new price set by the Company.</td>
                    </tr>   
                    <tr>
                        <td style="vertical-align: top; padding-right: 10px;">10. </td>
                        <td>The term of this Agreement shall begin as of the Effective Date and shall continue thereafter for a period of three (3) years. It includes yearly procurement for the STEM Programme(s) product.</td>
                    </tr>   
                    <tr>
                        <td style="vertical-align: top; padding-right: 10px;">11. </td>
                        <td>Any breach by the Customer of any of the provisions of this Contract shall entitle the Company, in addition to any rights or remedies that the Company may have in law or otherwise, to give notice to the Customer declaring this Contract null and void, after which all payment shall be forfeited to the Company as a penalty.</td>
                    </tr>   
                </table>
            </div>
        </div>
    </div>
    <div class="page-break">
        <div style="text-align: justify; position: relative;">
            <img src="{{ public_path('images/anf-stamp.jpg') }}" style="position: absolute; width: 150px; top: 150px; left: 200px;">
            By signing this contract, all parties agree to the Terms and Condition above. Alteration for this agreement can only be made by both parties and must be placed in writing. Both parties will receive a printed copy of this agreement, and will be responsible for upholding its terms.<br><br><br>
            <span style="font-weight: bold;">Signed</span> by the company<span style="padding-left: 150px;">)</span><br><br>
            <span style="font-weight: bold;">ALFA AND FRIENDS SDN BHD</span> (1176616-V)<br>
            36, Jalan BP 7/8,<br>
            Bandar Bukit Puchong,<br>
            47100 Puchong, Selangor,<br>
            MALAYSIA.<br>
            <span style="font-weight: bold;">Tel:</span> +6 (03) 8052 5198<br><br><br><br>
            <div style="position: relative">
                <span style="font-weight: bold;">Signed</span> by the Customer<span style="padding-left: 150px;">)</span><br><br><br><br><br><br><br><br><br>
                <img src="{{ $contract->customer_signature }}" style="position: absolute; height: 100px; top: 30px; left: 5px;">
            </div>
            <div style="position: relative">
                <span style="font-weight: bold;">Signed</span> by the Witness<span style="padding-left: 163px;">)</span><br><br>
                <img src="{{ $contract->witness_signature }}" style="position: absolute; height: 100px; top: 30px; left: 5px;">
            </div>
        </div>
    </div>
    <div class="page-break">
        <div style="text-align: right; font-style: italic; font-weight: bold; margin-bottom: 20px;">
            Appendix 1
        </div>
        <div style="text-align: justify;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tr>
                    <td style="text-align: center; border: 1px solid black; font-weight: bold;">ACTIONS</td>
                    <td style="text-align: center; border: 1px solid black; font-weight: bold;">SERVICES RECEIVE</td>
                </tr>
                <tr>
                    <td style="text-align: justify; border: 1px solid black; padding-left: 8px; padding-right: 8px; padding-bottom: 5px;">The contract shall be deemed signed upon full payment of the Right-to-Use Fee as stipulated in clause (a).</td>
                    <td style="text-align: justify; border: 1px solid black; padding-left: 8px; padding-right: 8px; padding-bottom: 5px;">The provision of programme(s) adopted set, inclusive of sales kit materials, shall be in accordance with the specifications outlined in <span style="font-style: italic; font-weight: bold;">Appendix 2</span>.</td>
                </tr>
                <tr>
                    <td style="text-align: justify; border: 1px solid black; padding-left: 8px; padding-right: 8px; padding-bottom: 5px;">The first order of the Little Scientists Programme shall adhere to the quantity specified in clause (b.ii).<br><br>The following order does not include the Teaching Aids.</td>
                    <td style="text-align: justify; border: 1px solid black; padding-left: 8px; padding-right: 8px; padding-bottom: 5px;">
                        <ol style="padding-left: 15px; padding-top: 0px; margin: 0;">
                            <li>Three (3) sets of Teaching Aids, with one set allocated for each Level.</li>
                            <li>Six (6) books of Lesson Plans distributed across 3 Levels, with two books designated for each level.</li>
                            <li>Student Kit comprising an activity book, the quantity of which shall align with the submitted order.</li>
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: justify; border: 1px solid black; padding-left: 8px; padding-right: 8px; padding-bottom: 5px;">
                        The first purchase of the Little Bot Programme shall adhere to the quantity specified in clause (b.ii).<br><br>
                        The following order does not include the Teaching Aids.</td>
                    <td style="text-align: justify; border: 1px solid black; padding-left: 8px; padding-right: 8px; padding-bottom: 5px;">
                        <ol style="padding-left: 15px; padding-top: 0px; margin: 0;">
                            <li>One (1) set of Teaching Aids allocated for use in Two (2) Levels.</li>
                            <li>Four (4) books of Lesson Plans distributed across 2 Levels, with two books designated for each level.</li>
                            <li>Student Kit comprising an activity book, the quantity of which shall align with the submitted order.</li>
                        </ol>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="page-break">
        <div style="text-align: right; font-style: italic; font-weight: bold; margin-bottom: 20px;">
            Appendix 2
        </div>
        <table style="text-align: justify; width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="text-align: center; border: 1px solid black; font-weight: bold;" colspan="3">SIGN UP SET - STEM PROGRAMME PACKAGE</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">NO</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="96%">ITEM</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">QTY</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Banner</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">2</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">STEM Plaque</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">3</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Flyers (Integrated STEM)</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">50</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">4</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Flyers (Toddler)</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">30</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">5</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Little Scientists Programme Sample Books (BM/BC)</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">6</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">6</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Little Scientists Programme Student Kit Sample</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">7</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Euler Maths Programme Sample Books (BM/BC)</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">6</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">8</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Euler Maths Programme Student Kit Sample</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">9</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Little Bot Programme Sample Books</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">4</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">10</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Little Bot Programme Student Kit Sample</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">11</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Toddler Programme Student Kit Sample</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">12</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Globe</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">13</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Human Torso</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">14</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Stethoscope</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">15</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Skeleton</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">16</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Thermometer</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">17</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Magnet Attraction Set</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">18</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Teeth Model</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">19</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Circuit Tester</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">20</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Bug Viewer</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">21</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Farm Animals Model</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">22</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Insects Animals Model</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">23</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Wild Animals Model</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">24</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Rectangular Plastic Container</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
        </table>
        <div style="font-style: italic; font-size: 11px; margin-top: 5px;">
            The items listed for the Science Corner are subject to change or be replaced based on availability.
        </div>
    </div>
    <div>
        <table style="text-align: justify; width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="text-align: center; border: 1px solid black; font-weight: bold;" colspan="3">SIGN UP SET - LITTLE SCIENTISTS PROGRAMME</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">NO</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="96%">ITEM</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">QTY</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Little Scientists Programme Sample Books (BM/BC)</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">6</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">2</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Little Scientists Programme Student Kit Sample</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
        </table>
        <div style="font-style: italic; font-size: 11px; margin-top: 5px; margin-bottom: 30px;">
            The items listed for the Sign-Up Set are subject to change or be replaced based on availability.
        </div>
        <table style="text-align: justify; width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="text-align: center; border: 1px solid black; font-weight: bold;" colspan="3">SIGN UP SET – LITTLE BOT PROGRAMME</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">NO</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="96%">ITEM</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">QTY</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Little Bot Programme Sample Books</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">6</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">2</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Little Bot Programme Student Kit Sample</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
        </table>
        <div style="font-style: italic; font-size: 11px; margin-top: 5px; margin-bottom: 30px;">
            The items listed for the Sign-Up Set are subject to change or be replaced based on availability.
        </div>
        <table style="text-align: justify; width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="text-align: center; border: 1px solid black; font-weight: bold;" colspan="3">SIGN UP SET – EULER MATHS PROGRAMME</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">NO</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="96%">ITEM</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">QTY</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Euler Maths Programme Sample Books</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">6</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">2</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Euler Maths Programme Student Kit Sample</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
        </table>
        <div style="font-style: italic; font-size: 11px; margin-top: 5px; margin-bottom: 30px;">
            The items listed for the Sign-Up Set are subject to change or be replaced based on availability.
        </div>
        <table style="text-align: justify; width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="text-align: center; border: 1px solid black; font-weight: bold;" colspan="3">SIGN UP SET – TODDLER PROGRAMME</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">NO</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="96%">ITEM</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;" width="2%">QTY</td>
            </tr>
            <tr>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
                <td style="text-align: left; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Toddler Programme Student Kit Sample</td>
                <td style="text-align: center; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">1</td>
            </tr>
        </table>
        <div style="font-style: italic; font-size: 11px; margin-top: 5px; margin-bottom: 30px;">
            The items listed for the Sign-Up Set are subject to change or be replaced based on availability.
        </div>
    </div>
    </div>
</body>
</html> 