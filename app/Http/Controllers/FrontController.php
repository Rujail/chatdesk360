<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactLead;
use App\Models\Package;
use App\Mail\ContactLeadMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class FrontController extends Controller
{
    public function home()
    {
        $seoTitle = "ChatDesk - Live Chat Software for Your Website";
        $seoDescription = "Convert visitors into customers with ChatDesk. Real-time live chat, agent monitoring, and advanced analytics.";
        $seoKeywords = "live chat, chat software, customer support tool";

        return view('front.home', compact('seoTitle', 'seoDescription', 'seoKeywords'));
    }

    public function features()
    {
        $seoTitle = "ChatDesk Features - Powerful Live Chat Tools to Scale Support";
        $seoDescription = "Discover ChatDesk's complete feature set: real-time monitoring, automated triggers, customizable widgets, and deep analytics to supercharge your team.";
        $seoKeywords = "live chat software features, customer service automation, real time chat widget, multi agent support desk";
        return view('front.features', compact('seoTitle', 'seoDescription', 'seoKeywords'));
    }

    public function about()
    {
        $seoTitle = "About ChatDesk - Our Mission and Team";
        $seoDescription = "Learn how ChatDesk started, meet our passionate team, and discover our mission to revolutionize customer support through real-time live chat solutions.";
        $seoKeywords = "about chatdesk, live chat company, customer support mission, chatdesk team";
        return view('front.about', compact('seoTitle', 'seoDescription', 'seoKeywords'));
    }

    public function productTour()
    {
        $seoTitle = "ChatDesk Features Tour - See How Our Live Chat Works";
        $seoDescription = "Take a tour of ChatDesk. Explore real-time agent monitoring, customizable chat widgets, and powerful analytics designed to boost conversions.";
        $seoKeywords = "live chat features, chatdesk product tour, agent monitoring tool, live chat analytics demo";
        return view('front.product-tour', compact('seoTitle', 'seoDescription', 'seoKeywords'));
    }

    public function pricing()
    {   
        $seoTitle = "ChatDesk Pricing - Simple, Transparent Plans for Every Business";
        $seoDescription = "Choose the perfect ChatDesk plan for your business. From free trials to advanced enterprise solutions, find the right live chat tools to scale your support.";
        $seoKeywords = "chatdesk pricing, live chat software cost, customer support plans, affordable live chat tool";

         // Fetch all packages ordered by price
        $packages = Package::orderBy('price', 'asc')->get();

        return view('front.pricing', compact('seoTitle', 'seoDescription', 'seoKeywords', 'packages'));
    }

    public function blog()
    {
        return view('front.blog');
    }

    public function blogShow($slug)
    {
        return view('front.blog-detail', compact('slug'));
    }

    public function contact()
    {
        $seoTitle = "Contact ChatDesk360 - Get in Touch With Our Support & Sales Team";
        $seoDescription = "Have questions about ChatDesk? Contact our customer support or sales team today. We're here to help you set up and optimize your live chat software.";
        $seoKeywords = "contact chatdesk, live chat support team, chatdesk sales, customer service hotline";
        return view('front.contact', compact('seoTitle', 'seoDescription', 'seoKeywords'));
    }

    public function contactSend(Request $request)
    {
        $validated = $request->validate([
            'cn'   => 'required|string|max:255',
            'lname'=> 'required|string|max:255',
            'em'   => 'required|email|max:255',
            'pn'   => 'required|string|max:20',
            'msg'  => 'required|string|max:2000',
        ], 
        [], // Second argument is for custom messages (we leave it empty)
        [
            // Third argument is for custom attribute names
            'cn'   => 'First Name',
            'lname'=> 'Last Name',
            'em'   => 'Email',
            'pn'   => 'Phone Number',
            'msg'  => 'Message',
        ]);

        $leadData = [
            'first_name' => $validated['cn'],
            'last_name'  => $validated['lname'],
            'email'      => $validated['em'],
            'phone'      => $validated['pn'],
            'message'    => $validated['msg'],
        ];

        // Store in database
        ContactLead::create($leadData);

        try {
            // Send the "Thank You" email to the USER who submitted the form
            Mail::to($leadData['email'])->send(new ContactLeadMail($leadData));
        } catch (\Exception $e) {
            // Log the error if email fails, but still show success to the user
            \Log::error('Contact email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your message. We will get back to you soon!'
        ]);
    }

    public function help()
    {
        $seoTitle = "ChatDesk Help Center - Documentation, Guides, and Support";
        $seoDescription = "Find answers, setup guides, and troubleshooting steps for ChatDesk. Get step-by-step documentation to optimize your live chat performance.";
        $seoKeywords = "chatdesk support, live chat documentation, help desk guides, chat widget setup tutorial";
        return view('front.help', compact('seoTitle', 'seoDescription', 'seoKeywords'));
    }
}