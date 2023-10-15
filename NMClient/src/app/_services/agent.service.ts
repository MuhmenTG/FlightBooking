import { Injectable } from '@angular/core';
import { HttpService } from './http.service';
import { environment } from 'src/environments/environment.development';
import { Observable } from 'rxjs';
import { EnquiryResponse, EnquiryResponses } from '../_models/Enquiries/EnquiryResponse';
import { BookingResponse, Passenger } from '../_models/Employees/Agent/Booking';
import { FinalBookingResponse } from '../_models/Flights/FinalBookingResponse';
import { EnquiryReply } from '../_models/Employees/Agent/EnquiryReply';

@Injectable({
  providedIn: 'root'
})
export class AgentService extends HttpService {
  private apiUrl = environment.apiUrl + "/travelAgent";

  getAllFlightBookings(): Observable<BookingResponse> {
    return this.http.get<BookingResponse>(this.apiUrl + "/getAllFlightBookings", this.httpOptionsAccount);
  }

  getBooking(bookingReference: string): Observable<FinalBookingResponse> {
    return this.http.get<FinalBookingResponse>(this.apiUrl + "/getBooking/" + bookingReference, this.httpOptionsAccount);
  }

  cancelFlightBooking(bookingReference: string): Observable<any> {
    return this.http.get<any>(this.apiUrl + "/cancelFlight/" + bookingReference, this.httpOptionsAccount);
  }

  getAllPaymentTransactions(): Observable<any> {
    return this.http.get<any>(this.apiUrl + "/getAllPaymentTransactions", this.httpOptionsAccount);
  }

  getSpecificPaymentTransactions(bookingReference: string, paymentId: string): Observable<any> {
    return this.http.get<any>(this.apiUrl + "/getSpecificPaymentTransactions/" + bookingReference + "/" + paymentId, this.httpOptionsAccount);
  }

  editPassengerInformation(passengerInfo: Passenger): Observable<Passenger> {
    return this.http.post<Passenger>(this.apiUrl + "/editPassengerInformation", passengerInfo, this.httpOptionsAccount);
  }

  sendBookingToCustomer(): Observable<any> {
    return this.http.post<any>(this.apiUrl + "/sendBooking", this.httpOptionsAccount);
  }

  answerUserEnquiry(reply: EnquiryReply): Observable<any> {
    return this.http.post<any>(this.apiUrl + "/answerUserEnquiry", reply, this.httpOptionsAccount);
  }

  setEnquiryStatus(id: number): Observable<any> {
    return this.http.put<any>(this.apiUrl + "/setUserEnquiryStatus/" + id, null, this.httpOptionsAccount);
  }

  deleteEnquiryStatus(id: number): Observable<any> {
    return this.http.delete<any>(this.apiUrl + "/removeUserEnquiry/" + id, this.httpOptionsAccount);
  }

  getAllEnquiries(): Observable<EnquiryResponses> {
    return this.http.get<EnquiryResponses>(this.apiUrl + "/getAllUserEnquries", this.httpOptionsAccount);
  }

  getSpecificEnquiry(id: number): Observable<EnquiryResponse> {
    return this.http.get<EnquiryResponse>(this.apiUrl + "/getSpecificUserEnquiry/" + id, this.httpOptionsAccount);
  }

  editAccount(): Observable<any> {
    return this.http.post<any>(this.apiUrl + "/editAgentDetails", this.httpOptions);
  }
}
