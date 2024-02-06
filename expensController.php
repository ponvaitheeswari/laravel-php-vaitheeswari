<?php

namespace App\Http\Controllers;
use App\Models\expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;
use Illuminate\Support\Facades\File;

class expensController extends Controller
{
    public function sendError($message)
    {
        $response['message'] = $message;
        return response()->json($response, 200);
    }
    public function sendSuccessMessage($message, $response)
    {
        return response()->json([
            'result' => $response,
            'message' => $message,
        ]);
    }

    public function checkUserIdExist($request)
    {
        $data['user_id'] = isset($request) ? $request : '';
        $validation = Validator::make($data, [
            'user_id' => 'unique:users,user_id|',
        ]);
        if ($validation->fails()) {
            return true;
        } else {
            return false;
        }
    }
    public function AddExpenses(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'shop_name' => 'required',
               'amount' =>'required',
            'status' => 'required',
            'created_by' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Invalid');
        }

        if (!$this->checkUserIdExist($input['created_by'])) {
            return $this->sendError("Invalid user....");
        }
        $imagePath = "";

        if (isset($input['image']) && $input['image'] != "") {
            $imagePath = str::random(10) . ".png";
            $path = "tastydots_old/public/uploads/images/" . $imagePath;
            $fileData = base64_decode($input['image']);
            File::put($_SERVER['DOCUMENT_ROOT'] . '/' . $path, $fileData);
        }
        $expense = new expense();
        $expense->exp_id   =  uniqid();
        $expense->image   =  $imagePath;
        $expense->shop_name = $input['shop_name'];
        $expense->amount =$input['amount'];
        $expense->status     =  $input['status'];
        $expense->created_by    =  $input['created_by'];
        if ($expense->save()) {
            return $this->sendSuccessMessage($input['amount'] . ' store Successfully ', "1");
        } else {
            return $this->sendError('Try Again...');
        }
    }
    public function getAllExpenes(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Invalid Params');
        }
        if ($this->checkUserIdExist($input['user_id'])) {
            $expense = expense::where('status', '1')->get();
            return $this->sendSuccessMessage('expenes list Successfully', $expense);
        } else {
            return $this->sendError('Invalid user...');
        }
    }
    public function UpdateExpense(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'exp_id' => 'required',
            'shop_name' => 'required',
            'amount'=>'required',
            'status' => 'required',
            'updated_by' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError("Invalid Params");
        }
        if (!$this->checkUserIdExist($input['updated_by'])) {
            return $this->sendError("Invalid user....");
        }
        $imagePath = "";

        if (isset($input['image']) && $input['image'] != "") {
            $imagePath = str::random(10) . ".png";
            $path = "tastydots_old/public/uploads/images/" . $imagePath;
            $fileData = base64_decode($input['image']);
            File::put($_SERVER['DOCUMENT_ROOT'] . '/' . $path, $fileData);
        }
        if ($expense = expense::where('exp_id', $input['exp_id'])->first()) {
            $expense->shop_name =$input['shop_name'];
            $expense->amount    =  $input['amount'];
            if ($imagePath != "") {
                $expense->image   =  $imagePath;
            }
            $expense->status           =  $input['status'];
            $expense->updated_by            =  $input['updated_by'];
            if ($expense->update()){
                return $this->sendSuccessMessage($input['shop_name'] . ' update successfully','1');
            }else{
                return $this->sendError("invalid name...try again");
            }
        } else {
            return $this->sendError("Invalid....");
        }
    }
    public function deleteExpense(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Invalid Params..');
        }else {
            if (expense::where('exp_id', $input['exp_id']->delete())) {
                return $this->sendSuccessMessage('details deleted Successfully...');
            } else {
                return $this->sendError('some misstakes...');
            }
        }
    }
}
