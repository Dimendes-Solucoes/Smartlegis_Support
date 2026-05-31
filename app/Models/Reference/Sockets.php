<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class Sockets extends Model
{
    const SessionStart = 1;
    const SessionEnd = 2;

    const SendDoc = 3;

    const QuorumAddUser = 4;
    const QuorumRemoveUser = 5;

    const VoteStart = 6;
    const Voterecp = 7;
    const EndVote = 8;
    const NominalVote = 9;
    const DeleteVote = 42;
    const CancelVote = 43;

    const StartTribune = 10;
    const TribuneUserAdd = 11;
    const TribuneUserUpdate = 35;
    const TribuneUserDelete = 36;
    const EndSubscribeTribune = 12;
    const EndTribune = 13;
    const ReserveTalk = 53;
    const StartTalk = 14;
    const PauseTalk = 15;
    const BackPauseTalk = 16;
    const FinishTalk = 17;
    const TimerExtra = 18;
    const ReopenTribune = 48;

    const AddApartUser = 19;
    const EndApart = 20;

    const ExplanationStart = 21;
    const ExplanationAddUser = 22;
    const ExplanationTimeF = 23;
    const ExplanationFinish = 24;

    const DiscussionStart = 25;
    const DiscussionAddUser = 26;
    const DiscussionFirstTimeF = 27;
    const DiscussionSecondTime = 28;
    const DiscussionEndTime = 29;
    const DiscussionFinish = 30;

    const QuestionOrderStart = 31;
    const QuestionOrderAddUser = 32;
    const QuestionOrderTimeF = 33;
    const QuestionOrderFinish = 34;

    const UrgentDocumentStartVoting = 37;
    const UrgentDocumentEndVoting = 38;
    const UrgentDocumentRecepVote = 39;
    const UrgentDocumentUsers = 40;
    const UrgentDocumentCancelVoting = 41;

    const SecondUrgentDocumentStartVoting = 44;
    const SecondUrgentDocumentEndVoting = 45;
    const SecondUrgentDocumentRecepVote = 46;
    const SecondUrgentDocumentCancelVoting = 47;

    const ExpiredProtocolReservation = 49;

    const DocumentReadInTheSession = 50;
    const DocumentApprovedInTheSession = 51;
    const DocumentUnapprovedInTheSession = 52;
    const DocumentChangeOrder = 54;

    const RefreshAllPages = 55;
    const RefreshTVPage = 56;
}
